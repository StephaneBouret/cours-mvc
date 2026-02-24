<?php

/** @var App\Entities\Creation $creation */
/** @var string $form */
/** @var ?string $error */
?>

<h1>Modifier : <?= htmlspecialchars($creation->getTitle()) ?></h1>

<div class="container">
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($creation->getPicture()): ?>
        <p>
            <img src="/uploads/<?= htmlspecialchars($creation->getPicture()) ?>"
                alt="<?= htmlspecialchars($creation->getTitle()) ?>"
                style="max-width: 300px; height:auto;">
        </p>
    <?php endif; ?>

    <?= $form ?>

    <p>
        <a href="/?controller=creation&action=index">⬅ Retour</a>
    </p>
</div>