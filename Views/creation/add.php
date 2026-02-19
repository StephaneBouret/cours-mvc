<h1>Ajouter une création</h1>

<div class="container">
    <?php if (!empty($error)): ?>
        <p class="mb-2 text-danger"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?= $form ?>
</div>