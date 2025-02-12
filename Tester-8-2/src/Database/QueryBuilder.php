<?php
declare(strict_types=1);

namespace App\Database;

class QueryBuilder {
    private \mysqli $db;

    public function __construct(\mysqli $db) {
        $this->db = $db;
    }

    public function select(string $table, array $columns = ['*'], array $where = []): array {
        $columnsList = implode(', ', $columns);
        $sql = "SELECT {$columnsList} FROM {$table}";

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "{$key} = ?";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $stmt = $this->db->prepare($sql);

        if (!empty($where)) {
            $types = str_repeat('s', count($where));
            $stmt->bind_param($types, ...array_values($where));
        }

        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        
        $stmt = $this->db->prepare($sql);
        
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        
        $stmt->execute();
        
        return $this->db->insert_id;
    }
}