<?php

use DateTimeImmutable;

/** @var App\Entities\Creation $creation */
?>

<h1><?= htmlspecialchars($creation->getTitle()) ?></h1>

<?php if ($creation->getCreatedAt() instanceof DateTimeImmutable): ?>
    <p><small>Créée le : <?= htmlspecialchars($creation->getCreatedAt()->format('d/m/Y H:i')) ?></small></p>
<?php endif; ?>

<p><?= nl2br(htmlspecialchars($creation->getDescription())) ?></p>

<?php if ($creation->getPicture()): ?>
    <p>
        <img
            src="/uploads/<?= htmlspecialchars($creation->getPicture()) ?>"
            alt="<?= htmlspecialchars($creation->getTitle()) ?>"
            style="max-width: 400px; height: auto;">
    </p>
<?php endif; ?>

<p>
    <a href="/?controller=creation&action=edit&id=<?= $creation->getIdCreation() ?>">✏ Modifier</a>
    |
    <a href="/?controller=creation&action=index">⬅ Retour</a>
</p>