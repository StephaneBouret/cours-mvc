<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon portfolio - liste de mes créations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <header class="text-center">
            <h1>MON PORTFOLIO</h1>
        </header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Mon portfolio</a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                aria-current="page"
                                href="#">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Mes créations</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <main>
            <h2>Liste de mes créations</h2>
            <?php if (empty($creations)): ?>
                <div class="alert alert-info">
                    Aucune création pour le moment.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($creations as $creation): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($creation->getPicture()): ?>
                                    <img
                                        src="/uploads/<?= htmlspecialchars($creation->getPicture()) ?>"
                                        class="card-img-top"
                                        alt="<?= htmlspecialchars($creation->getTitle()) ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($creation->getTitle()) ?>
                                    </h5>
                                    <p class="card-text">
                                        <?= htmlspecialchars($creation->getDescription()) ?>
                                    </p>
                                </div>
                                <div class="card-footer text-muted">
                                    Créée le
                                    <?= $creation->getCreatedAt()->format('d/m/Y') ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
        <footer class="text-center">
            <p>Mon portfolio Copyright ©2026</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>