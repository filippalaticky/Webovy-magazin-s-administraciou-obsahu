<?php

declare(strict_types=1);

namespace Models;

use PDO;

class Post
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getPublicPosts(int $limit = 6): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT id, title, slug, excerpt, content, cover_image_url, status, is_featured, created_at, updated_at, published_at
                FROM posts
                WHERE status = 'published'
                ORDER BY is_featured DESC, published_at DESC, created_at DESC
                LIMIT {$limit}";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getFeaturedPosts(int $limit = 3): array
    {
        $limit = max(1, $limit);
        $sql = "SELECT id, title, slug, excerpt, content, cover_image_url, status, is_featured, created_at, updated_at, published_at
                FROM posts
                WHERE status = 'published' AND is_featured = 1
                ORDER BY published_at DESC, created_at DESC
                LIMIT {$limit}";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getAdminPosts(): array
    {
        $sql = 'SELECT id, title, slug, excerpt, content, cover_image_url, status, is_featured, created_at, updated_at, published_at
                FROM posts
                ORDER BY is_featured DESC, created_at DESC';

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getStats(): array
    {
        $sql = 'SELECT
                    COUNT(*) AS total_posts,
                    SUM(CASE WHEN status = "published" THEN 1 ELSE 0 END) AS published_posts,
                    SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) AS draft_posts,
                    SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) AS featured_posts
                FROM posts';

        $row = $this->pdo->query($sql)->fetch() ?: [];

        return [
            'total_posts' => (int) ($row['total_posts'] ?? 0),
            'published_posts' => (int) ($row['published_posts'] ?? 0),
            'draft_posts' => (int) ($row['draft_posts'] ?? 0),
            'featured_posts' => (int) ($row['featured_posts'] ?? 0),
        ];
    }

    public function getById(int $id): ?array
    {
        $sql = 'SELECT id, title, slug, excerpt, content, cover_image_url, status, is_featured, created_at, updated_at, published_at
                FROM posts
                WHERE id = :id
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch();

        return $post ?: null;
    }

    public function create(array $data): bool
    {
        $title = trim((string) ($data['title'] ?? ''));
        $slug = $this->generateUniqueSlug($title);
        $status = $this->normalizeStatus((string) ($data['status'] ?? 'draft'));
        $isFeatured = !empty($data['is_featured']) ? 1 : 0;
        $excerpt = trim((string) ($data['excerpt'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $coverImageUrl = trim((string) ($data['cover_image_url'] ?? ''));

        if ($excerpt === '') {
            $excerpt = $this->buildExcerpt($content);
        }

        $sql = 'INSERT INTO posts (title, slug, excerpt, content, cover_image_url, status, is_featured, created_at, updated_at, published_at)
                VALUES (:title, :slug, :excerpt, :content, :cover_image_url, :status, :is_featured, NOW(), NOW(), :published_at)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':excerpt' => $excerpt,
            ':content' => $content,
            ':cover_image_url' => $coverImageUrl !== '' ? $coverImageUrl : null,
            ':status' => $status,
            ':is_featured' => $isFeatured,
            ':published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $title = trim((string) ($data['title'] ?? ''));
        $slug = $this->generateUniqueSlug($title, $id);
        $status = $this->normalizeStatus((string) ($data['status'] ?? 'draft'));
        $isFeatured = !empty($data['is_featured']) ? 1 : 0;
        $excerpt = trim((string) ($data['excerpt'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $coverImageUrl = trim((string) ($data['cover_image_url'] ?? ''));
        $current = $this->getById($id);

        if ($current === null) {
            return false;
        }

        if ($excerpt === '') {
            $excerpt = $this->buildExcerpt($content);
        }

        $publishedAt = $current['published_at'] ?? null;
        if ($status === 'published' && $publishedAt === null) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        if ($status === 'draft') {
            $publishedAt = null;
        }

        $sql = 'UPDATE posts
                SET title = :title,
                    slug = :slug,
                    excerpt = :excerpt,
                    content = :content,
                    cover_image_url = :cover_image_url,
                    status = :status,
                    is_featured = :is_featured,
                    updated_at = NOW(),
                    published_at = :published_at
                WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':slug' => $slug,
            ':excerpt' => $excerpt,
            ':content' => $content,
            ':cover_image_url' => $coverImageUrl !== '' ? $coverImageUrl : null,
            ':status' => $status,
            ':is_featured' => $isFeatured,
            ':published_at' => $publishedAt,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function toggleStatus(int $id): ?string
    {
        $post = $this->getById($id);

        if ($post === null) {
            return null;
        }

        $newStatus = $post['status'] === 'published' ? 'draft' : 'published';
        $publishedAt = $newStatus === 'published' ? date('Y-m-d H:i:s') : null;

        $stmt = $this->pdo->prepare('UPDATE posts SET status = :status, published_at = :published_at, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            ':id' => $id,
            ':status' => $newStatus,
            ':published_at' => $publishedAt,
        ]);

        return $newStatus;
    }

    public function toggleFeatured(int $id): ?int
    {
        $post = $this->getById($id);

        if ($post === null) {
            return null;
        }

        $newValue = (int) ((int) $post['is_featured'] === 1 ? 0 : 1);
        $stmt = $this->pdo->prepare('UPDATE posts SET is_featured = :is_featured, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            ':id' => $id,
            ':is_featured' => $newValue,
        ]);

        return $newValue;
    }

    private function normalizeStatus(string $status): string
    {
        return in_array($status, ['draft', 'published'], true) ? $status : 'draft';
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = $this->slugify($title);
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT id FROM posts WHERE slug = :slug';
        $params = [':slug' => $slug];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $ignoreId;
        }

        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    private function slugify(string $value): string
    {
        $value = trim($value);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $ascii = $ascii !== false ? $ascii : $value;
        $ascii = strtolower($ascii);
        $ascii = preg_replace('/[^a-z0-9]+/', '-', $ascii) ?? '';
        $ascii = trim($ascii, '-');

        return $ascii !== '' ? $ascii : 'post-' . bin2hex(random_bytes(3));
    }

    private function buildExcerpt(string $content): string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? '');

        if ($plain === '') {
            return '';
        }

        if (mb_strlen($plain) <= 180) {
            return $plain;
        }

        return mb_substr($plain, 0, 177) . '...';
    }
}