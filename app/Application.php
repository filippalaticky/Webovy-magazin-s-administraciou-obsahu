<?php

declare(strict_types=1);

namespace App;

use Controllers\AuthController;
use Controllers\ContentController;
use Controllers\TaskController;

class Application
{
    public function __construct(
        private AuthController $authController,
        private ContentController $contentController,
        private TaskController $taskController,
        private \App\UrlGenerator $urlGenerator
    ) {
    }

    public function run(): void
    {
        $action = isset($_GET['action']) ? (string) $_GET['action'] : 'home';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        switch ($action) {
            case 'home':
                $this->contentController->home();
                break;

            case 'admin':
                $this->contentController->dashboard();
                break;

            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->authController->login($_POST);
                    break;
                }

                $this->authController->showLogin();
                break;

            case 'logout':
                $this->authController->logout();
                break;

            case 'post-create':
                $this->contentController->createForm();
                break;

            case 'post-store':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->contentController->store($_POST);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'post-edit':
                if ($id > 0) {
                    $this->contentController->editForm($id);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'post-update':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                    $this->contentController->update($id, $_POST);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'post-delete':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                    $this->contentController->destroy($id, $_POST);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'post-toggle-status':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                    $this->contentController->toggleStatus($id, $_POST);
                    break;
                }

                http_response_code(405);
                echo 'Method not allowed';
                break;

            case 'post-toggle-featured':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                    $this->contentController->toggleFeatured($id, $_POST);
                    break;
                }

                http_response_code(405);
                echo 'Method not allowed';
                break;

            case 'tasks':
                $this->taskController->index($_GET);
                break;

            case 'task-create':
                $this->taskController->createForm();
                break;

            case 'task-store':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->taskController->store($_POST);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'task-edit':
                if ($id > 0) {
                    $this->taskController->editForm($id);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'task-update':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                    $this->taskController->update($id, $_POST);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'task-delete':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                    $this->taskController->destroy($id, $_POST);
                    break;
                }

                $this->redirectToAdmin();
                break;

            case 'task-toggle':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
                    $this->taskController->toggleAjax($id, $_POST);
                    break;
                }

                http_response_code(405);
                echo 'Method not allowed';
                break;

            default:
                $this->redirectHome();
        }
    }

    private function redirectToAdmin(): void
    {
        header('Location: ' . $this->urlGenerator->indexUrl(['action' => 'admin']));
        exit;
    }

    private function redirectHome(): void
    {
        header('Location: ' . $this->urlGenerator->indexUrl(['action' => 'home']));
        exit;
    }
}