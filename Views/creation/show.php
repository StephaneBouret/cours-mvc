<?php
use App\Core\Csrf;
/** @var App\Entities\Creation $creation */
?>

<h1><?= htmlspecialchars($creation->getTitle()) ?></h1>

<?php if ($creation->getCreatedAt() instanceof \DateTimeImmutable): ?>
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
<p>
    <form method="post" action="/?controller=creation&action=delete&id=<?= $creation->getIdCreation() ?>"
        style="display:inline;">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(Csrf::token('creation_delete_' . $creation->getIdCreation())) ?>">
        <button type="submit" onclick="return confirm('Supprimer ?');">🗑 Supprimer</button>
    </form>
</p>