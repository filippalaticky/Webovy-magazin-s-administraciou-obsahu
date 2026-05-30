<?php

declare(strict_types=1);

namespace Controllers;

class BaseController
{
    protected function render(string $viewPath, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $viewPath . '.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function ensureCsrfToken(): void
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function checkCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    protected function requireLogin(): void
    {
        $this->requireAdmin();
    }

    protected function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect(app_index_url(['action' => 'login']));
        }
    }
}
