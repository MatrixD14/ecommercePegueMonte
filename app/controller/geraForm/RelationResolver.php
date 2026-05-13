<?php
class RelationResolver
{
    public function resolveDisplayValue($value, array $columnConfig): string
    {
        if (empty($value)) return '—';

        $chain = $this->extractJoinChain($columnConfig);
        if (empty($chain)) return (string)$value;

        $db = Database::connects();
        $currentValue = $value;

        foreach ($chain as $level => $step) {
            $sql = "SELECT {$step['displayColumn']}";
            if (isset($step['nextForeignKey'])) {
                $sql .= ", {$step['nextForeignKey']}";
            }
            $sql .= " FROM {$step['table']} WHERE {$step['primaryKey']} = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $currentValue);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if (!$row) return "Não encontrado";

            if (!isset($chain[$level + 1])) {
                return $row[$step['displayColumn']] ?? (string)$currentValue;
            }

            if (isset($step['nextForeignKey']) && isset($row[$step['nextForeignKey']])) {
                $currentValue = $row[$step['nextForeignKey']];
            } else {
                break;
            }
        }
        return (string)$currentValue;
    }
    public function getSelectOptions(array $columnConfig): array
    {
        $chain = $this->extractJoinChain($columnConfig);
        if (empty($chain)) {
            return $this->getStaticOptions($columnConfig);
        }

        $first = $chain[0];
        $db = Database::connects();
        $sql = "SELECT {$first['primaryKey']}, {$first['displayColumn']} 
                FROM {$first['table']} 
                ORDER BY {$first['displayColumn']} ASC";
        $res = $db->query($sql);
        $options = [];
        while ($row = $res->fetch_assoc()) {
            $options[$row[$first['primaryKey']]] = $row[$first['displayColumn']];
        }
        return $options;
    }

    private function extractJoinChain(array $columnConfig): array
    {
        if (!empty($columnConfig['relation'])) {
            $rel = $columnConfig['relation'];
            return [[
                'table' => $rel['tabela'],
                'primaryKey' => $rel['value'] ?? 'id',
                'displayColumn' => $rel['coluna'] ?? $columnConfig['display_column'] ?? 'nome',
            ]];
        }
        if (!empty($columnConfig['foreign']) && isset($columnConfig['join']) && is_array($columnConfig['join'])) {
            $joinArray = $columnConfig['join'];
            $chain = [];

            $isIndexed = (array_keys($joinArray) !== range(0, count($joinArray) - 1));

            if ($isIndexed) {
                ksort($joinArray); // ordena pelos índices
                $previousTable = null;
                foreach ($joinArray as $level => $map) {
                    if (!is_array($map)) continue;
                    $keys = array_keys($map);
                    $values = array_values($map);
                    if (count($keys) < 2) continue;

                    $foreignTable = $keys[0];
                    $primaryKey   = $values[0];
                    $localColumn  = $values[1];

                    $step = [
                        'table' => $foreignTable,
                        'primaryKey' => $primaryKey,
                        'displayColumn' => $columnConfig['display_column'] ?? 'nome',
                        'localColumn' => $localColumn,
                    ];

                    $nextLevelMap = $joinArray[$level + 1] ?? null;
                    if ($nextLevelMap && is_array($nextLevelMap)) {
                        $nextValues = array_values($nextLevelMap);
                        if (isset($nextValues[1])) {
                            $step['nextForeignKey'] = $nextValues[1];
                        }
                    }
                    $chain[] = $step;
                    $previousTable = $foreignTable;
                }
            } else {
                $map = $joinArray[0] ?? null;
                if ($map && is_array($map)) {
                    $keys = array_keys($map);
                    $values = array_values($map);
                    $chain[] = [
                        'table' => $keys[0],
                        'primaryKey' => $values[0],
                        'displayColumn' => $columnConfig['display_column'] ?? 'nome',
                        'localColumn' => $values[1] ?? null,
                    ];
                }
            }
            return $chain;
        }
        return [];
    }

    private function getStaticOptions(array $columnConfig): array
    {
        if (empty($columnConfig['options']) || !is_array($columnConfig['options'])) {
            return [];
        }
        $options = [];
        foreach ($columnConfig['options'] as $key => $value) {
            if (is_array($value)) {
                $options[$key] = $key;
            } else {
                $options[is_numeric($key) ? $value : $key] = $value;
            }
        }
        return $options;
    }
}
