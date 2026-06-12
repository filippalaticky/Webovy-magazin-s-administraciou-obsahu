<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';

/**
 * @var array $tasks
 * @var string|null $statusFilter
 * @var string $sort
 */
?>
<section class="toolbar fade-in">
    <a class="btn" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'task-create'])) ?>">+ Nova uloha</a>

    <form method="get" action="<?= $escaper->escape($urlGenerator->indexUrl()) ?>" class="filters">
        <input type="hidden" name="action" value="tasks">

        <label for="status">Filter</label>
        <select name="status" id="status">
            <option value="" <?= $statusFilter === null ? 'selected' : '' ?>>Vsetko</option>
            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Nehotove</option>
            <option value="done" <?= $statusFilter === 'done' ? 'selected' : '' ?>>Hotove</option>
        </select>

        <label for="sort">Triedenie</label>
        <select name="sort" id="sort">
            <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Najnovsie</option>
            <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Najstarsie</option>
        </select>

        <button class="btn btn-secondary" type="submit">Pouzit</button>
    </form>
</section>

<section class="task-grid">
    <?php if (empty($tasks)): ?>
        <article class="card empty fade-in">
            <h3>Zatial ziadne ulohy</h3>
            <p>Pridaj prvu ulohu a zacni planovat.</p>
        </article>
    <?php endif; ?>

    <?php foreach ($tasks as $task): ?>
        <article class="card task-card fade-in" data-task-id="<?= (int) $task['id'] ?>">
            <header>
                <h3><?= $escaper->escape((string) $task['title']) ?></h3>
                <span class="badge <?= $task['status'] === 'done' ? 'done' : 'pending' ?>" data-role="status-badge">
                    <?= $escaper->escape((string) $task['status']) ?>
                </span>
            </header>

            <p><?= nl2br($escaper->escape((string) $task['description'])) ?></p>
            <small>Vytvorene: <?= $escaper->escape((string) $task['created_at']) ?></small>

            <div class="actions">
                <button
                    class="btn btn-secondary js-toggle-status"
                    type="button"
                    data-id="<?= (int) $task['id'] ?>"
                >
                    Prepnut stav
                </button>
                <a class="btn btn-secondary" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'task-edit', 'id' => (int) $task['id']])) ?>">Upravit</a>

                <form method="post" action="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'task-delete', 'id' => (int) $task['id']])) ?>" onsubmit="return confirm('Naozaj vymazat?');">
                    <input type="hidden" name="csrf_token" value="<?= $escaper->escape((string) ($_SESSION['csrf_token'] ?? '')) ?>">
                    <button class="btn danger" type="submit">Vymazat</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
