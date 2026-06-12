<?php

declare(strict_types=1);

namespace Controllers;

use Models\Post;

class ContentController extends BaseController
{
    public function __construct(\App\UrlGenerator $urlGenerator, \App\Escaper $escaper, private Post $postModel)
    {
        parent::__construct($urlGenerator, $escaper);
    }

    public function home(): void
    {
        $posts = $this->postModel->getPublicPosts(6);
        $featuredPosts = $this->postModel->getFeaturedPosts(3);
        $stats = $this->postModel->getStats();

        $this->render('public/home', [
            'pageTitle' => 'Atelier Nova',
            'layoutMode' => 'public',
            'posts' => $posts,
            'featuredPosts' => $featuredPosts,
            'stats' => $stats,
        ]);
    }

    public function dashboard(): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        $this->render('admin/dashboard', [
            'pageTitle' => 'Admin panel',
            'layoutMode' => 'admin',
            'posts' => $this->postModel->getAdminPosts(),
            'stats' => $this->postModel->getStats(),
        ]);
    }

    public function createForm(array $errors = [], array $old = []): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        $this->render('admin/form', [
            'pageTitle' => 'Novy prispevok',
            'layoutMode' => 'admin',
            'errors' => $errors,
            'old' => $old,
            'post' => null,
            'formAction' => $this->urlGenerator->indexUrl(['action' => 'post-store']),
        ]);
    }

    public function store(array $postData): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->createForm(['Neplatny CSRF token.'], $postData);
            return;
        }

        $errors = $this->validatePostInput($postData);
        if (!empty($errors)) {
            $this->createForm($errors, $postData);
            return;
        }

        $this->postModel->create($postData);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
    }

    public function editForm(int $id, array $errors = [], array $old = []): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        $post = $this->postModel->getById($id);
        if ($post === null) {
            $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
        }

        $this->render('admin/form', [
            'pageTitle' => 'Upravit prispevok',
            'layoutMode' => 'admin',
            'errors' => $errors,
            'old' => $old,
            'post' => $post,
            'formAction' => $this->urlGenerator->indexUrl(['action' => 'post-update', 'id' => $id]),
        ]);
    }

    public function update(int $id, array $postData): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->editForm($id, ['Neplatny CSRF token.'], $postData);
            return;
        }

        $errors = $this->validatePostInput($postData);
        if (!empty($errors)) {
            $this->editForm($id, $errors, $postData);
            return;
        }

        $this->postModel->update($id, $postData);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
    }

    public function destroy(int $id, array $postData): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
        }

        $this->postModel->delete($id);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
    }

    public function toggleStatus(int $id, array $postData): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
        }

        $this->postModel->toggleStatus($id);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
    }

    public function toggleFeatured(int $id, array $postData): void
    {
        $this->requireAdmin();
        $this->ensureCsrfToken();

        if (!$this->checkCsrfToken((string) ($postData['csrf_token'] ?? ''))) {
            $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
        }

        $this->postModel->toggleFeatured($id);
        $this->redirect($this->urlGenerator->indexUrl(['action' => 'admin']));
    }

    private function validatePostInput(array $postData): array
    {
        $errors = [];
        $title = trim((string) ($postData['title'] ?? ''));
        $excerpt = trim((string) ($postData['excerpt'] ?? ''));
        $content = trim((string) ($postData['content'] ?? ''));
        $coverImageUrl = trim((string) ($postData['cover_image_url'] ?? ''));
        $status = (string) ($postData['status'] ?? 'draft');

        if ($title === '') {
            $errors[] = 'Nazov prispevku je povinny.';
        }

        if (mb_strlen($title) > 180) {
            $errors[] = 'Nazov moze mat maximalne 180 znakov.';
        }

        if ($excerpt !== '' && mb_strlen($excerpt) > 320) {
            $errors[] = 'Perex moze mat maximalne 320 znakov.';
        }

        if ($content === '') {
            $errors[] = 'Obsah prispevku je povinny.';
        }

        if (mb_strlen($content) > 12000) {
            $errors[] = 'Obsah moze mat maximalne 12000 znakov.';
        }

        if ($coverImageUrl !== '' && !filter_var($coverImageUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'URL obrazku nie je platna.';
        }

        if (!in_array($status, ['draft', 'published'], true)) {
            $errors[] = 'Neplatny stav prispevku.';
        }

        return $errors;
    }
}