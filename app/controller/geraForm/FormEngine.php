<?php
class FormEngine
{
    private array $config;
    private array $dbData = [];
    private bool $isLocked = false;
    private array $user;
    private string $hoje;
    private FieldRenderer $renderer;
    public function __construct(string $tableKey, $id = null, $user = null, $manualReadonly = false, array $presetData = [])
    {
        $allConfigs = require __DIR__ . '/ConfDefinition.php';
        if (!isset($allConfigs[$tableKey])) {
            throw new Exception("Tabela '$tableKey' não configurada.");
        }
        $this->config = $allConfigs[$tableKey];
        $this->user = $user ?? ['privilegio' => 'aluno', 'id' => null, 'nome' => 'Visitante'];
        $this->hoje = date('Y-m-d');

        // Corrige possíveis 'laber' para 'label'
        foreach ($this->config['colunas'] as &$col) {
            if (isset($col['laber']) && !isset($col['label'])) {
                $col['label'] = $col['laber'];
            }
        }

        if ($id) {
            $this->loadData($id);
        } else {
            $this->dbData = $presetData;
        }

        // Regra de bloqueio: data passada ou força manual
        $dataRef = $this->dbData['dia'] ?? null;
        if (($dataRef && $dataRef < $this->hoje) || $manualReadonly) {
            $this->isLocked = true;
        }

        $this->renderer = new FieldRenderer($this->isLocked, $this->user, $this->dbData, $this->config['colunas']);
    }

    private function loadData($id): void
    {
        $db = Database::connects();
        $tabela = $this->config['tabela'];
        $stmt = $db->prepare("SELECT * FROM $tabela WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $this->dbData = $stmt->get_result()->fetch_assoc() ?? [];
    }

    public function render(): string
    {
        $html = $this->renderLockedAlert();

        foreach ($this->config['colunas'] as $name => $colConfig) {
            if (!empty($colConfig['primary'])) continue;
            $html .= $this->renderer->renderField($name, $colConfig);
        }
        return $html;
    }

    private function renderLockedAlert(): string
    {
        $dataRef = $this->dbData['dia'] ?? null;
        if ($this->isLocked && $dataRef && $dataRef < $this->hoje) {
            return '<div class="alert-locked">
                        <strong>Registro Antigo:</strong> 
                        Este item é do passado e não pode ser alterado.
                    </div>';
        }
        return '';
    }
    public function canSubmit(): bool
    {
        $dataRef = $this->dbData['dia'] ?? null;
        if (!$dataRef) return true;
        if ($dataRef < $this->hoje) return false;
        $periodoConfig = $this->config['colunas']['periodo']['options'] ?? null;
        if (is_array($periodoConfig)) {
            $maiorMax = 0;
            foreach ($periodoConfig as $key => $option) {
                if (is_array($option) && isset($option['max'])) {
                    $maiorMax = max($maiorMax, (int)$option['max']);
                } else {
                    $maiorMax = 22; // padrão
                }
            }
            $horaAtual = (int)date('H');
            if ($horaAtual >= $maiorMax) {
                return false;
            }
        }
        return true;
    }

    public function getDados(): array
    {
        return $this->dbData;
    }
}
