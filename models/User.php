<?php

declare(strict_types=1);

namespace Models;

use PDO;

class User
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByUsername(string $username): ?array
    {
        $sql = 'SELECT id, username, password_hash, created_at FROM users WHERE username = :username LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function create(string $username, string $plainPassword): bool
    {
        $sql = 'INSERT INTO users (username, password_hash, created_at) VALUES (:username, :password_hash, NOW())';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':password_hash' => password_hash($plainPassword, PASSWORD_DEFAULT),
        ]);
    }

    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) AS cnt FROM users';
        $result = $this->pdo->query($sql)->fetch();

        return (int) ($result['cnt'] ?? 0);
    }

    public function createDefaultAdminIfNeeded(): void
    {
        if ($this->countAll() > 0) {
            return;
        }

        $this->create('admin', 'admin123');
    }
}
