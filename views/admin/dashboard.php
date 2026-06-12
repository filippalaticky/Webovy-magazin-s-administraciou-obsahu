<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';

/**
 * @var array $stats
 * @var array $posts
 */
?>
<section class="admin-layout">
    <aside class="admin-rail fade-in">
        <span class="eyebrow">Control room</span>
        <h2>Admin panel</h2>
        <p>Rýchla správa verejného obsahu, featured postov a stavu publikovania.</p>

        <div class="admin-stats">
            <article class="stat-card compact">
                <strong><?= (int) ($stats['total_posts'] ?? 0) ?></strong>
                <span>prispevkov</span>
            </article>
            <article class="stat-card compact">
                <strong><?= (int) ($stats['published_posts'] ?? 0) ?></strong>
                <span>publikovanych</span>
            </article>
            <article class="stat-card compact">
                <strong><?= (int) ($stats['draft_posts'] ?? 0) ?></strong>
                <span>draftov</span>
            </article>
        </div>

        <a class="btn" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'post-create'])) ?>">Novy prispevok</a>
    </aside>

    <section class="admin-content">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Content overview</span>
                <h2>Všetky články a rýchle akcie.</h2>
            </div>
        </div>

        <div class="admin-list">
            <?php foreach ($posts as $post): ?>
                <article class="admin-post fade-in">
                    <div class="admin-post-main">
                        <div class="post-topline">
                            <span class="badge <?= $post['status'] === 'published' ? 'done' : 'pending' ?>"><?= $escaper->escape((string) $post['status']) ?></span>
                            <?php if (!empty($post['is_featured'])): ?>
                                <span class="badge featured">featured</span>
                            <?php endif; ?>
                        </div>
                        <h3><?= $escaper->escape((string) $post['title']) ?></h3>
                        <p><?= $escaper->escape((string) $post['excerpt']) ?></p>
                        <small>Slug: <?= $escaper->escape((string) $post['slug']) ?> · Aktualizovane: <?= $escaper->escape((string) $post['updated_at']) ?></small>
                    </div>

                    <div class="admin-actions">
                        <a class="btn btn-secondary" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'post-edit', 'id' => (int) $post['id']])) ?>">Upravit</a>
                        <form method="post" action="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'post-toggle-status', 'id' => (int) $post['id']])) ?>">
                            <input type="hidden" name="csrf_token" value="<?= $escaper->escape((string) ($_SESSION['csrf_token'] ?? '')) ?>">
                            <button class="btn btn-secondary" type="submit">
                                <?= $post['status'] === 'published' ? 'Stiahnut' : 'Publikovat' ?>
                            </button>
                        </form>
                        <form method="post" action="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'post-toggle-featured', 'id' => (int) $post['id']])) ?>">
                            <input type="hidden" name="csrf_token" value="<?= $escaper->escape((string) ($_SESSION['csrf_token'] ?? '')) ?>">
                            <button class="btn btn-secondary" type="submit">
                                <?= !empty($post['is_featured']) ? 'Odfeatured' : 'Featured' ?>
                            </button>
                        </form>
                        <form method="post" action="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'post-delete', 'id' => (int) $post['id']])) ?>" onsubmit="return confirm('Naozaj vymazat prispevok?');">
                            <input type="hidden" name="csrf_token" value="<?= $escaper->escape((string) ($_SESSION['csrf_token'] ?? '')) ?>">
                            <button class="btn danger" type="submit">Vymazat</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>