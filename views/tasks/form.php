<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';

$titleValue = (string) ($old['title'] ?? ($task['title'] ?? ''));
$descriptionValue = (string) ($old['description'] ?? ($task['description'] ?? ''));
$statusValue = (string) ($old['status'] ?? ($task['status'] ?? 'pending'));
$isEdit = $task !== null;
?>
<section class="card fade-in">
    <h2><?= $isEdit ? 'Upravit ulohu' : 'Nova uloha' ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert">
            <?php foreach ($errors as $error): ?>
                <p><?= e((string) $error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e((string) $formAction) ?>" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e((string) ($_SESSION['csrf_token'] ?? '')) ?>">

        <label for="title">Nazov</label>
        <input id="title" name="title" type="text" maxlength="255" value="<?= e($titleValue) ?>" required>

        <label for="description">Popis</label>
        <textarea id="description" name="description" rows="6" maxlength="5000"><?= e($descriptionValue) ?></textarea>

        <?php if ($isEdit): ?>
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="pending" <?= $statusValue === 'pending' ? 'selected' : '' ?>>pending</option>
                <option value="done" <?= $statusValue === 'done' ? 'selected' : '' ?>>done</option>
            </select>
        <?php endif; ?>

        <div class="actions">
            <button type="submit" class="btn"><?= $isEdit ? 'Ulozit zmeny' : 'Vytvorit ulohu' ?></button>
            <a class="btn btn-secondary" href="<?= e(app_index_url(['action' => 'tasks'])) ?>">Spat</a>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
