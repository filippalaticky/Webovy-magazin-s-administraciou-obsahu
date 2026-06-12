<?php

declare(strict_types=1);

namespace Models;

use PDO;

class User
{
    private ?string $tableName = null;

    public function __construct(private PDO $pdo)
    {
    }

    public function findByUsername(string $username): ?array
    {
        $table = $this->getTableName();
        $sql = "SELECT id, username, password_hash, created_at FROM {$table} WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function create(string $username, string $plainPassword): bool
    {
        $table = $this->getTableName();
        $sql = "INSERT INTO {$table} (username, password_hash, created_at) VALUES (:username, :password_hash, NOW())";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':password_hash' => password_hash($plainPassword, PASSWORD_DEFAULT),
        ]);
    }

    public function countAll(): int
    {
        try {
            $table = $this->getTableName();
            $sql = "SELECT COUNT(*) AS cnt FROM {$table}";
            $result = $this->pdo->query($sql)->fetch();

            return (int) ($result['cnt'] ?? 0);
        } catch (\PDOException) {
            return 0;
        }
    }

    public function createDefaultAdminIfNeeded(): void
    {
        try {
            if ($this->countAll() > 0) {
                return;
            }

            $this->create('admin', 'admin123');
        } catch (\PDOException) {
        }
    }

    private function getTableName(): string
    {
        if ($this->tableName !== null) {
            return $this->tableName;
        }

        foreach (['users', 'todo_app_users'] as $candidate) {
            if ($this->tableExists($candidate)) {
                $this->tableName = $candidate;

                return $this->tableName;
            }
        }

        $this->tableName = 'users';

        return $this->tableName;
    }

    private function tableExists(string $tableName): bool
    {
        try {
            $sql = 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':table_name' => $tableName]);

            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException) {
            return false;
        }
    }
}
