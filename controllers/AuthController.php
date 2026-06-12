<?php

declare(strict_types=1);

namespace Controllers;

use App\Escaper;
use App\UrlGenerator;
use Models\User;

class AuthController extends BaseController
{
    public function __construct(UrlGenerator $urlGenerator, Escaper $escaper, private User $userModel)
    {
        parent::__construct($urlGenerator, $escaper);
    }

    public function showLogin(array $errors = []): void
    {
        $this->ensureCsrfToken();
        $this->render('auth/login', [
            'pageTitle' => 'Admin pristup',
            'layoutMode' => 'admin',
            'errors' => $errors,
        ]);
    }

    public function login(array $postData): void
    {
        $this->ensureCsrfToken();

        $token = $postData['csrf_token'] ?? '';
        if (!$this->checkCsrfToken((string) $token)) {
            $this->showLogin(['Neplatny CSRF token.']);
            return;
        }

        $username = trim((string) ($postData['username'] ?? ''));
        $password = (string) ($postData['password'] ?? '');

        $errors = [];

        if ($username === '' || $password === '') {
            $errors[] = 'Vyplnte meno aj heslo.';
            $this->showLogin($errors);
            return;
        }

        $user = $this->userModel->findByUsername($username);
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Nespravne prihlasovacie udaje.';
            $this->showLogin($errors);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];

        $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'home']));
    }
}
