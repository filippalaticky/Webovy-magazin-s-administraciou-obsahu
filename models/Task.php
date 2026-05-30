<?php

declare(strict_types=1);

namespace Models;

use PDO;

class Task
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(string $title, string $description): bool
    {
        $sql = 'INSERT INTO tasks (title, description, status, created_at) VALUES (:title, :description, :status, NOW())';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':status' => 'pending',
        ]);
    }

    public function getAll(?string $statusFilter = null, string $sort = 'desc'): array
    {
        $allowedSort = ['asc', 'desc'];
        $sort = strtolower($sort);

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'desc';
        }

        if ($statusFilter !== null && in_array($statusFilter, ['pending', 'done'], true)) {
            $sql = "SELECT id, title, description, status, created_at
                    FROM tasks
                    WHERE status = :status
                    ORDER BY created_at {$sort}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':status' => $statusFilter]);

            return $stmt->fetchAll();
        }

        $sql = "SELECT id, title, description, status, created_at
                FROM tasks
                ORDER BY created_at {$sort}";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = 'SELECT id, title, description, status, created_at FROM tasks WHERE id = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $task = $stmt->fetch();

        return $task ?: null;
    }

    public function update(int $id, string $title, string $description, string $status): bool
    {
        $sql = 'UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':description' => $description,
            ':status' => $status,
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

    public function toggleStatus(int $id): ?string
    {
        $task = $this->getById($id);

        if ($task === null) {
            return null;
        }

        $newStatus = $task['status'] === 'done' ? 'pending' : 'done';

        $sql = 'UPDATE tasks SET status = :status WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':status' => $newStatus,
        ]);

        return $newStatus;
    }
}
