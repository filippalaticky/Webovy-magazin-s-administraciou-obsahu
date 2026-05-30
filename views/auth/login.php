<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';
?>
<section class="card auth-card fade-in">
    <h2>Admin pristup</h2>
    <p class="muted">Predvolene udaje: admin / admin123. Prihlasenie je chranene hashovanim hesla, session cookie a CSRF tokenom.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert">
            <?php foreach ($errors as $error): ?>
                <p><?= e((string) $error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(app_index_url(['action' => 'login'])) ?>" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e((string) ($_SESSION['csrf_token'] ?? '')) ?>">

        <label for="username">Meno</label>
        <input id="username" name="username" type="text" maxlength="120" required>

        <label for="password">Heslo</label>
        <input id="password" name="password" type="password" required>

        <button type="submit" class="btn">Prihlasit sa</button>
    </form>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
