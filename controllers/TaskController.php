<?php

declare(strict_types=1);

namespace Controllers;

use Models\Task;

class TaskController extends BaseController
{
    public function __construct(\App\UrlGenerator $urlGenerator, \App\Escaper $escaper, private Task $taskModel)
    {
        parent::__construct($urlGenerator, $escaper);
    }

    public function index(array $query): void
    {
        $this->requireLogin();
        $this->ensureCsrfToken();

        $status = isset($query['status']) ? (string) $query['status'] : '';
        $sort = isset($query['sort']) ? (string) $query['sort'] : 'desc';

        $statusFilter = in_array($status, ['pending', 'done'], true) ? $status : null;
        $sort = in_array(strtolower($sort), ['asc', 'desc'], true) ? strtolower($sort) : 'desc';

        $tasks = $this->taskModel->getAll($statusFilter, $sort);

        $this->render('tasks/list', [
            'pageTitle' => 'To-Do List',
            'tasks' => $tasks,
            'statusFilter' => $statusFilter,
            'sort' => $sort,
        ]);
    }

    public function createForm(array $errors = [], array $old = []): void
    {
        $this->requireLogin();
        $this->ensureCsrfToken();

        $this->render('tasks/form', [
            'pageTitle' => 'Nova uloha',
            'errors' => $errors,
            'old' => $old,
            'task' => null,
            'formAction' => $this->urlGenerator->indexUrl(['action' => 'task-store']),
        ]);
    }

    public function store(array $postData): void
    {
        $this->requireLogin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->createForm(['Neplatny CSRF token.'], $postData);
            return;
        }

        $title = trim((string) ($postData['title'] ?? ''));
        $description = trim((string) ($postData['description'] ?? ''));

        $errors = $this->validateTaskInput($title, $description);
        if (!empty($errors)) {
            $this->createForm($errors, $postData);
            return;
        }

        $this->taskModel->create($title, $description);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'tasks']));
    }

    public function editForm(int $id, array $errors = [], array $old = []): void
    {
        $this->requireLogin();
        $this->ensureCsrfToken();

        $task = $this->taskModel->getById($id);
        if ($task === null) {
            $this->redirect($this->urlGenerator->indexUrl(['action' => 'tasks']));
        }

        $this->render('tasks/form', [
            'pageTitle' => 'Upravit ulohu',
            'errors' => $errors,
            'old' => $old,
            'task' => $task,
            'formAction' => $this->urlGenerator->indexUrl(['action' => 'task-update', 'id' => $id]),
        ]);
    }

    public function update(int $id, array $postData): void
    {
        $this->requireLogin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->editForm($id, ['Neplatny CSRF token.'], $postData);
            return;
        }

        $title = trim((string) ($postData['title'] ?? ''));
        $description = trim((string) ($postData['description'] ?? ''));
        $status = (string) ($postData['status'] ?? 'pending');

        if (!in_array($status, ['pending', 'done'], true)) {
            $status = 'pending';
        }

        $errors = $this->validateTaskInput($title, $description);
        if (!empty($errors)) {
            $this->editForm($id, $errors, $postData);
            return;
        }

        $this->taskModel->update($id, $title, $description, $status);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'tasks']));
    }

    public function destroy(int $id, array $postData): void
    {
        $this->requireLogin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->redirect($this->urlGenerator->indexUrl(['action' => 'tasks']));
        }

        $this->taskModel->delete($id);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'tasks']));
    }

    public function toggleAjax(int $id, array $postData): void
    {
        $this->requireLogin();
        $this->ensureCsrfToken();

        header('Content-Type: application/json');

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $status = $this->taskModel->toggleStatus($id);

        if ($status === null) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'message' => 'Task not found']);
            return;
        }

        echo json_encode(['ok' => true, 'status' => $status]);
    }

    private function validateTaskInput(string $title, string $description): array
    {
        $errors = [];

        if ($title === '') {
            $errors[] = 'Nazov ulohy je povinny.';
        }

        if (mb_strlen($title) > 255) {
            $errors[] = 'Nazov ulohy moze mat maximalne 255 znakov.';
        }

        if (mb_strlen($description) > 5000) {
            $errors[] = 'Popis moze mat maximalne 5000 znakov.';
        }

        return $errors;
    }
}
