<?php
class FieldRenderer
{
    private bool $readonly;
    private array $user;
    private array $currentData;
    private array $allColumns;
    private RelationResolver $relationResolver;

    public function __construct(bool $readonly, array $user, array $currentData, array $allColumns)
    {
        $this->readonly = $readonly;
        $this->user = $user;
        $this->currentData = $currentData;
        $this->allColumns = $allColumns;
        $this->relationResolver = new RelationResolver();
    }

    public function renderField(string $name, array $config): string
    {
        $type = $config['type'] ?? 'text';
        $label = $config['label'] ?? $config['laber'] ?? ucfirst($name);
        $value = $this->currentData[$name] ?? ($config['default'] ?? '');
        $id = $this->sanitizeId($name);
        $required = !empty($config['required']) ? 'required' : '';

        // Caso especial: campo idUser para usuário logado
        if ($name === 'idUser' && empty($value) && in_array($this->user['privilegio'], ['aluno', 'professor'])) {
            $value = $this->user['id'];
            $config['type'] = 'readonly_user';
        }

        $fieldHtml = match ($type) {
            'hidden'      => $this->renderHidden($name, $value),
            'readonly_user' => $this->renderReadonlyUser($name, $value, $label),
            'readonly'    => $this->renderReadonly($name, $value, $config),
            'date'        => $this->renderDate($name, $value, $id, $required),
            'select'      => $this->renderSelect($name, $value, $config, $id),
            'file'        => $this->renderFile($name, $value, $id, $config),
            default       => $this->renderInput($type, $name, $value, $id, $required, $config),
        };

        $html = '<div class="form-field">';
        if ($type !== 'hidden') {
            $html .= "<label for='$id'>$label</label></br>";
        }
        $html .= $fieldHtml;
        if (!empty($config['help'])) {
            $html .= "<small class='field-help'>{$config['help']}</small>";
        }
        $html .= '</div>';
        return $html;
    }

    private function renderInput(string $type, string $name, $value, string $id, string $required, array $config): string
    {
        $attrs = [];
        if ($this->readonly) {
            $attrs[] = 'readonly';
            $attrs[] = "style='cursor:not-allowed;'";
        }
        if ($type === 'number') {
            if (isset($config['step'])) $attrs[] = "step='{$config['step']}'";
            if (isset($config['min']))   $attrs[] = "min='{$config['min']}'";
            if (isset($config['max']))   $attrs[] = "max='{$config['max']}'";
        }
        $attrStr = implode(' ', $attrs);
        return "<input type='$type' name='$name' id='$id' value='" . htmlspecialchars($value) . "' $required $attrStr class='input-dados'>";
    }

    private function renderHidden(string $name, $value): string
    {
        return "<input type='hidden' name='$name' value='" . htmlspecialchars($value) . "'>";
    }

    private function renderReadonlyUser(string $name, $value, string $label): string
    {
        $userName = $this->user['nome'] ?? $value;
        return $this->renderHidden($name, $value) .
            "<input type='text' class='input-dados' value='" . htmlspecialchars($userName) . "' readonly disabled>";
    }

    private function renderReadonly(string $name, $value, array $config): string
    {
        $display = $this->relationResolver->resolveDisplayValue($value, $config);
        return $this->renderHidden($name, $value) .
            "<input type='text' class='input-dados' value='" . htmlspecialchars($display) . "' readonly disabled>";
    }

    private function renderDate(string $name, $value, string $id, string $required): string
    {
        $today = date('Y-m-d');
        $min = !$this->readonly ? "min='$today'" : "";
        $readonlyAttr = $this->readonly ? "readonly style='cursor:not-allowed;'" : "";
        // Converte de dd/mm/aaaa se necessário
        if (!empty($value) && preg_match('/\d{2}\/\d{2}\/\d{4}/', $value)) {
            $parts = explode('/', $value);
            $value = "$parts[2]-$parts[1]-$parts[0]";
        }
        return "<input type='date' name='$name' id='$id' value='$value' $required $min $readonlyAttr class='input-dados'>";
    }



    private function renderSelect(string $name, $value, array $config, string $id): string
    {
        // Se for readonly, mostra só o texto (já está ok)
        if ($this->readonly) {
            $display = $this->relationResolver->resolveDisplayValue($value, $config);
            return $this->renderHidden($name, $value) .
                "<input type='text' value='$display' readonly disabled class='input-dados'>";
        }

        // Obtém a configuração da relação (se existir)
        $rel = $config['relation'] ?? null;

        // Gera o label que vai aparecer no trigger
        $displayLabel = $this->getSelectDisplayLabel($name, $config, $value);

        // Data attributes para o JavaScript (se houver relação)
        $relDataAttrs = '';
        if ($rel) {
            $relDataAttrs = "data-tabela='{$rel['tabela']}' 
                         data-coluna='{$rel['coluna']}' 
                         data-value-col='{$rel['value']}'";
        }

        $html = "<div class='custom-select-container' id='container-$name' $relDataAttrs>
                <input type='hidden' name='$name' id='hidden-$name' value='" . htmlspecialchars($value) . "'>
                <input type='text' class='custom-select-trigger select-dados' 
                       id='$name' value='" . htmlspecialchars($displayLabel) . "' 
                       readonly autocomplete='off' style='cursor: pointer;'>
                <div class='custom-select-options'>
                    <input class='custom-search input-dados' name='search-$name' 
                           id='search-$name' placeholder='digite dois caracteres...' 
                           oninput='pesquisarSelect(this.value)'>
                    <div class='selected-item-top-container'>";

        if (!empty($value) && $displayLabel !== 'Selecione...') {
            $html .= "<div class='selected-badge'>
                      <small>selecionado antes*:</small>
                      <div class='custom-option selected' data-value='" . htmlspecialchars($value) . "'>$displayLabel</div>
                  </div>";
        }

        $html .= "</div><div class='options-scroll-area'>
              <div class='custom-option default-option' data-value=''>Selecione...</div>";

        // Adiciona opções estáticas (se existirem)
        $html .= $this->renderStaticOptions($config);

        // Adiciona opções do banco (se tiver relation)
        if ($rel) {
            $html .= $this->renderDatabaseOptions($rel);
        }

        $html .= "</div></div></div>";
        return $html;
    }

    private function renderFile(string $name, $currentFile, string $id, array $config): string
    {
        $html = '<div class="file-upload-wrapper">';

        // Campo fake (text) que mostra o nome do arquivo
        $displayValue = empty($currentFile) ? 'Seleciona um arquivo' : basename($currentFile);
        $html .= '<input type="text" class="input-dados fake-file-field" 
                    id="fake-' . $id . '" 
                    value="' . htmlspecialchars($displayValue) . '" 
                    readonly 
                    placeholder="Seleciona um arquivo"
                    data-target="' . $id . '">';

        $accept = $this->getAcceptAttribute($config);
        $html .= '<input type="file" name="' . $name . '" id="' . $id . '" class="real-file-input" ' . $accept . ' style="display: none;">';

        if (!empty($currentFile) && !$this->readonly) {
            $html .= '<div class="current-file-info">
                    <label class="delete-file-checkbox">
                        <input type="checkbox" name="delete_' . $name . '" value="1">
                        Excluir arquivo atual
                    </label>
                  </div>';
        }

        $html .= '</div>';
        return $html;
    }
    private function getAcceptAttribute(array $config): string
    {
        $extensions = $config['format_type'] ?? $config['accepted_types'] ?? [];
        if (empty($extensions)) return '';
        $accept = '.' . implode(',.', $extensions);
        return 'accept="' . $accept . '"';
    }

    private function getSelectDisplayLabel(string $name, array $config, $selected): string
    {
        if (empty($selected)) return "Selecione...";

        $rel = $config['relation'] ?? null;
        if ($rel) {
            // Busca o nome na tabela relacionada
            $db = Database::connects();
            $sql = "SELECT {$rel['coluna']} FROM {$rel['tabela']} WHERE {$rel['value']} = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $selected);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                return htmlspecialchars($row[$rel['coluna']]);
            }
            return "Selecione...";
        }

        // Se tiver opções estáticas
        if (!empty($config['options'])) {
            foreach ($config['options'] as $key => $regra) {
                $optVal = is_array($regra) ? $key : (is_numeric($key) ? $regra : $regra);
                if ((string)$optVal === (string)$selected) {
                    $optLabel = is_array($regra) ? $key : (is_numeric($key) ? $regra : $key);
                    return htmlspecialchars($optLabel);
                }
            }
        }

        return "Selecione...";
    }

    private function renderStaticOptions(array $config): string
    {
        if (empty($config['options']) || !is_array($config['options'])) {
            return "";
        }
        $html = "";
        $hoje = date('Y-m-d');
        $horaAtual = (int)date('H');
        $isHoje = ($this->currentData['dia'] ?? null) === $hoje;

        foreach ($config['options'] as $key => $regra) {
            $optLabel = is_numeric($key) ? $regra : $key;
            $optVal   = is_array($regra) ? $optLabel : (is_numeric($key) ? $regra : $regra);

            $classDisabled = "";
            $labelExibicao = htmlspecialchars($optLabel);

            // Se for um período com min/max
            if (is_array($regra) && isset($regra['min'], $regra['max'])) {
                $min = $regra['min'];
                $max = $regra['max'];
                if ($isHoje && ($horaAtual < $min || $horaAtual >= $max)) {
                    $classDisabled = "option-disabled";
                    $labelExibicao .= " *";
                }
            }
            $html .= "<div class='custom-option $classDisabled' data-value='$optVal'>$labelExibicao</div>";
        }
        return $html;
    }

    private function renderDatabaseOptions(array $rel): string
    {
        $db = Database::connects();
        $sql = "SELECT {$rel['value']}, {$rel['coluna']} 
            FROM {$rel['tabela']} 
            ORDER BY LENGTH({$rel['coluna']}) ASC LIMIT 50";
        $res = $db->query($sql);
        if (!$res) return "";

        $html = "";
        while ($row = $res->fetch_assoc()) {
            $val = $row[$rel['value']];
            $label = htmlspecialchars($row[$rel['coluna']]);
            $html .= "<div class='custom-option' data-value='$val'>$label</div>";
        }
        return $html;
    }
    private function sanitizeId(string $name): string
    {
        return preg_replace('/[^a-z0-9_-]/i', '_', $name);
    }
}
