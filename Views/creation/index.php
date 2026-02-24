<h2>Liste de mes créations</h2>
<?php if (empty($creations)): ?>
    <div class="alert alert-info">Aucune création pour le moment.</div>
<?php else: ?>
    <div class="row">
        <div class="d-flex">
            <a class="btn btn-primary mb-5 w-25" href="/?controller=creation&action=add" role="button">Créer</a>
        </div>
        <?php foreach ($creations as $creation): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($creation->getPicture()): ?>
                        <img
                            src="/uploads/<?= htmlspecialchars($creation->getPicture(), ENT_QUOTES, 'UTF-8') ?>"
                            class="card-img-top"
                            alt="<?= htmlspecialchars($creation->getTitle(), ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= htmlspecialchars(
                                $creation->getTitle(),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h5>
                        <p class="card-text">
                            <?= htmlspecialchars($creation->getDescription(), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <a href="/?controller=creation&action=show&id=<?= $creation->getIdCreation() ?>" class="btn btn-primary">Détail</a>
                    </div>
                    <div class="card-footer text-muted">
                        Créée le <?= $creation->getCreatedAt()->format('d/m/Y') ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>