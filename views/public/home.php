<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';

$highlightPost = $featuredPosts[0] ?? ($posts[0] ?? null);
?>
<section class="hero grid-2">
    <div class="hero-copy fade-in">
        <span class="eyebrow">Verejná stránka</span>
        <h1>Prémiový webový magazín s editovateľným obsahom.</h1>
        <p class="lead">Návštevník vidí čistú verejnú stránku, administrátor má za tlačidlom vpravo hore zabezpečený prístup do správy článkov.</p>

        <div class="hero-actions">
            <a class="btn" href="<?= e(app_index_url(['action' => 'admin'])) ?>">Otvorit admin panel</a>
            <a class="btn btn-secondary" href="#posts">Pozriet obsah</a>
        </div>

        <div class="stat-row">
            <article class="stat-card">
                <strong><?= (int) ($stats['total_posts'] ?? 0) ?></strong>
                <span>prispevkov spolu</span>
            </article>
            <article class="stat-card">
                <strong><?= (int) ($stats['published_posts'] ?? 0) ?></strong>
                <span>verejnych prispevkov</span>
            </article>
            <article class="stat-card">
                <strong><?= (int) ($stats['featured_posts'] ?? 0) ?></strong>
                <span>top highlightov</span>
            </article>
        </div>
    </div>

    <aside class="hero-panel fade-in">
        <?php if ($highlightPost !== null): ?>
            <span class="section-label">Featured story</span>
            <?php if (!empty($highlightPost['cover_image_url'])): ?>
                <img class="hero-image" src="<?= e((string) $highlightPost['cover_image_url']) ?>" alt="<?= e((string) $highlightPost['title']) ?>">
            <?php endif; ?>
            <h2><?= e((string) $highlightPost['title']) ?></h2>
            <p><?= e((string) $highlightPost['excerpt']) ?></p>
            <div class="meta-row">
                <span><?= e((string) $highlightPost['published_at']) ?></span>
                <span class="badge <?= !empty($highlightPost['is_featured']) ? 'featured' : 'regular' ?>">featured</span>
            </div>
        <?php else: ?>
            <span class="section-label">Editor note</span>
            <h2>Obsah pripravujeme.</h2>
            <p>Admin vie pridať prvý post v administračnom paneli.</p>
        <?php endif; ?>
    </aside>
</section>

<section class="section-heading" id="posts">
    <div>
        <span class="eyebrow">Latest stories</span>
        <h2>Čo je aktuálne na stránke.</h2>
    </div>
</section>

<section class="post-grid">
    <?php foreach ($posts as $post): ?>
        <article class="post-card <?= !empty($post['is_featured']) ? 'is-featured' : '' ?> fade-in">
            <?php if (!empty($post['cover_image_url'])): ?>
                <img class="post-cover" src="<?= e((string) $post['cover_image_url']) ?>" alt="<?= e((string) $post['title']) ?>">
            <?php endif; ?>
            <div class="post-body">
                <div class="post-topline">
                    <span class="badge <?= !empty($post['is_featured']) ? 'featured' : 'regular' ?>"><?= !empty($post['is_featured']) ? 'featured' : 'story' ?></span>
                    <span class="post-date"><?= e((string) $post['published_at']) ?></span>
                </div>
                <h3><?= e((string) $post['title']) ?></h3>
                <p><?= e((string) $post['excerpt']) ?></p>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="cta-band fade-in">
    <div>
        <span class="eyebrow">Admin access</span>
        <h2>Obsah spravuješ cez zabezpečený admin panel.</h2>
        <p>Prihlásenie je postavené na hashi hesla, session cookie a CSRF ochrane. Verejná stránka zostáva otvorená pre každého.</p>
    </div>
    <a class="btn" href="<?= e(app_index_url(['action' => 'admin'])) ?>">Prejsť do administrácie</a>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>