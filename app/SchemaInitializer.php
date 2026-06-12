<?php

declare(strict_types=1);

namespace App;

use PDO;

class SchemaInitializer
{
    public function ensureInitialized(PDO $pdo): void
    {
        if ($this->tablesAreReady($pdo)) {
            return;
        }

        $pdo->exec('DROP TABLE IF EXISTS posts');
        $pdo->exec('DROP TABLE IF EXISTS users');

        $schemaFile = __DIR__ . '/../sql/schema.sql';
        if (!is_file($schemaFile)) {
            return;
        }

        $sql = (string) file_get_contents($schemaFile);
        if ($sql === '') {
            return;
        }

        foreach ($this->splitStatements($sql) as $statement) {
            $normalized = ltrim($statement);

            if ($normalized === '') {
                continue;
            }

            if (preg_match('/^(CREATE\s+DATABASE|USE)\b/i', $normalized) === 1) {
                continue;
            }

            $pdo->exec($statement);
        }
    }

    private function tablesAreReady(PDO $pdo): bool
    {
        try {
            $usersCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
            $postsCount = (int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();

            return $usersCount >= 0 && $postsCount >= 0;
        } catch (\PDOException) {
            return false;
        }
    }

    /**
     * @return string[]
     */
    private function splitStatements(string $sql): array
    {
        $statements = explode(';', $sql);

        return array_values(array_filter(array_map('trim', $statements), static fn (string $statement): bool => $statement !== ''));
    }
}