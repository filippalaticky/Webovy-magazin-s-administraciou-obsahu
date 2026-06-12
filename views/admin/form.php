<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';

/**
 * @var array|null $post
 * @var string $formAction
 * @var array $errors
 * @var array $old
 */

$titleValue = (string) ($old['title'] ?? ($post['title'] ?? ''));
$excerptValue = (string) ($old['excerpt'] ?? ($post['excerpt'] ?? ''));
$contentValue = (string) ($old['content'] ?? ($post['content'] ?? ''));
$coverImageUrlValue = (string) ($old['cover_image_url'] ?? ($post['cover_image_url'] ?? ''));
$statusValue = (string) ($old['status'] ?? ($post['status'] ?? 'draft'));
$isFeaturedValue = !empty($old['is_featured'] ?? $post['is_featured'] ?? false);
$isEdit = $post !== null;
?>
<section class="editor-shell fade-in">
    <div class="section-heading">
        <div>
            <span class="eyebrow">Admin editor</span>
            <h2><?= $isEdit ? 'Upravit prispevok' : 'Novy prispevok' ?></h2>
        </div>
        <a class="btn btn-secondary" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'admin'])) ?>">Spat do panelu</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert">
            <?php foreach ($errors as $error): ?>
                <p><?= $escaper->escape((string) $error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $escaper->escape((string) $formAction) ?>" class="editor-grid">
        <input type="hidden" name="csrf_token" value="<?= $escaper->escape((string) ($_SESSION['csrf_token'] ?? '')) ?>">

        <label for="title">Nazov</label>
        <input id="title" name="title" type="text" maxlength="180" value="<?= $escaper->escape($titleValue) ?>" required>

        <label for="excerpt">Perex</label>
        <textarea id="excerpt" name="excerpt" rows="3" maxlength="320" placeholder="Krátky text do náhľadu"><?= $escaper->escape($excerptValue) ?></textarea>

        <label for="content">Obsah</label>
        <textarea id="content" name="content" rows="10" maxlength="12000" required><?= $escaper->escape($contentValue) ?></textarea>

        <label for="cover_image_url">URL obrazku</label>
        <input id="cover_image_url" name="cover_image_url" type="url" value="<?= $escaper->escape($coverImageUrlValue) ?>" placeholder="https://...">

        <div class="split-row">
            <div>
                <label for="status">Stav</label>
                <select name="status" id="status">
                    <option value="draft" <?= $statusValue === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $statusValue === 'published' ? 'selected' : '' ?>>Publikovany</option>
                </select>
            </div>

            <label class="checkbox-card" for="is_featured">
                <input id="is_featured" name="is_featured" type="checkbox" value="1" <?= $isFeaturedValue ? 'checked' : '' ?>>
                <span>
                    <strong>Featured post</strong>
                    <small>Vyzvihne sa na verejnej stránke.</small>
                </span>
            </label>
        </div>

        <div class="actions">
            <button type="submit" class="btn"><?= $isEdit ? 'Ulozit zmeny' : 'Vytvorit prispevok' ?></button>
            <a class="btn btn-secondary" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'admin'])) ?>">Zrusit</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>