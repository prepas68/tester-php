<?php
declare(strict_types=1);

namespace App\Config;

class Database {
    private string $host;
    private string $username;
    private string $password;
    private string $database;
    private ?\mysqli $connection = null;

    public function __construct(
        string $host = 'localhost',
        string $username = 'root',
        string $password = '',
        string $database = 'test'
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    public function connect(): \mysqli {
        if ($this->connection === null) {
            $this->connection = new \mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );

            if ($this->connection->connect_error) {
                throw new \Exception('Connection failed: ' . $this->connection->connect_error);
            }

            // Nastavenie character set na utf8mb4
            $this->connection->set_charset('utf8mb4');
        }

        return $this->connection;
    }

    public function __destruct() {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }
}