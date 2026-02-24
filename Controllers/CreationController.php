<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Form;
use App\Entities\Creation;
use App\Models\CreationModel;

final class CreationController extends Controller
{
    public function index(): void
    {
        $model = new CreationModel();
        $creations = $model->findAll();

        $this->render('creation/index', [
            'pageTitle' => 'Mon portfolio - liste de mes créations',
            'creations' => $creations
        ]);
    }

    public function show(int $id): void
    {
        $model = new CreationModel();
        $creation = $model->find($id);

        if ($creation === null) {
            http_response_code(404);
            echo '404 - Création introuvable';
            return;
        }

        $this->render('creation/show', [
            'title' => 'Détail création',
            'creation' => $creation,
        ]);
    }

    public function add(): void
    {
        $error = null;
        // ID CSRF stable pour ce formulaire
        $csrfId = 'creation_add';

        // 1) Traitement du POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? null;
            // 🔐 CSRF en premier (toujours)
            if (!Csrf::isValid($csrfId, is_string($token) ? $token : null)) {
                $error = 'CSRF invalide.';
            }
            // 🧪 validations métier
            elseif (!Form::validatePost($_POST, ['title', 'description'])) {
                $error = 'Tous les champs sont obligatoires';
            } elseif (!empty($_FILES['picture']['name']) && !Form::validateFiles($_FILES, ['picture'])) {
                $error = 'Image invalide';
            } else {
                // ✅ upload uniquement si un fichier est fourni
                $filename = null;
                if (!empty($_FILES['picture']['name'])) {
                    $filename = uniqid() . '_' . basename($_FILES['picture']['name']);

                    $ok = move_uploaded_file(
                        $_FILES['picture']['tmp_name'],
                        dirname(__DIR__) . '/public/uploads/' . $filename
                    );

                    if (!$ok) {
                        $error = "Erreur lors de l'upload de l'image";
                    }
                }

                // Si upload OK, on peut insérer
                if ($error === null) {
                    $creation = new Creation();
                    $creation->setTitle(trim($_POST['title']));
                    $creation->setDescription(trim($_POST['description']));

                    // ✅ picture nullable
                    if ($filename !== null) {
                        $creation->setPicture($filename);
                    }

                    // Délégation au Model
                    $model = new CreationModel();
                    $model->insert($creation);
                    header('Location: index.php?controller=creation&action=index');
                    // 'Location: index.php?controller=creation&action=show&id='
                    // . $created->getIdCreation()
                    exit;
                }
            }
        }

        // 2) GÉNÉRATION DU TOKEN (AFFICHAGE SEULEMENT)
        // On génère le token à l'affichage (GET) pour l'injecter dans le form
        $csrfToken = Csrf::token($csrfId);

        // 3) CONSTRUCTION DU FORMULAIRE
        // Form v2 : permet de re-remplir en cas d'erreur
        $form = new Form($_POST);
        $form
            ->startForm('index.php?controller=creation&action=add', 'POST', ['enctype' => 'multipart/form-data'])
            ->addInput('hidden', '_token', ['value' => $csrfToken])
            ->addLabel('title', 'Titre')
            ->addInput('text', 'title', ['id' => 'title', 'required' => true, 'class' => 'form-control'])
            ->addLabel('description', 'Description')
            ->addTextarea('description', '', ['rows' => 5, 'id' => 'description', 'class' => 'form-control'])
            ->addLabel('picture', 'Image')
            ->addInput('file', 'picture', ['class' => 'form-control'])
            ->addInput('submit', 'submit', ['value' => 'Ajouter', 'class' => 'mt-5 btn btn-primary'])
            ->endForm();

        $this->render('creation/add', [
            'form' => $form->getFormElements(),
            'error' => $error,
        ]);
    }

    public function edit(int $id): void
    {
        $error = null;

        $model = new CreationModel();
        $creation = $model->find($id);

        if (!$creation) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $csrfId = 'creation_edit_' . $id;

        // 1) POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? null;

            if (!Csrf::isValid($csrfId, is_string($token) ? $token : null)) {
                $error = 'CSRF invalide.';
            } elseif (!Form::validatePost($_POST, ['title', 'description'])) {
                $error = 'Tous les champs sont obligatoires';
            } elseif (!empty($_FILES['picture']['name']) && !Form::validateFiles($_FILES, ['picture'])) {
                $error = 'Image invalide';
            } else {
                $creation->setTitle(trim($_POST['title']));
                $creation->setDescription(trim($_POST['description']));

                if (!empty($_FILES['picture']['name'])) {
                    $filename = uniqid() . '_' . basename($_FILES['picture']['name']);

                    $ok = move_uploaded_file(
                        $_FILES['picture']['tmp_name'],
                        dirname(__DIR__) . '/public/uploads/' . $filename
                    );

                    if (!$ok) {
                        $error = "Erreur lors de l'upload de l'image";
                    } else {
                        $creation->setPicture($filename);
                    }
                }

                if ($error === null) {
                    $model->update($creation);
                    header('Location: index.php?controller=creation&action=show&id='
                        . $id);
                    exit;
                }
            }
        }

        // 2) Token d'affichage
        $csrfToken = Csrf::token($csrfId);

        // 3) Préremplissage : POST (si erreur) sinon entité
        $data = $_POST ?: [
            'title' => $creation->getTitle(),
            'description' => $creation->getDescription(),
        ];

        $form = new Form($data);
        $form
            ->startForm('index.php?controller=creation&action=edit&id=' . $id, 'POST', [
                'enctype' => 'multipart/form-data',
            ])
            ->addInput('hidden', '_token', ['value' => $csrfToken])
            ->addLabel('title', 'Titre')
            ->addInput('text', 'title', ['required' => true, 'class' => 'form-control'])
            ->addLabel('description', 'Description')
            ->addTextarea('description', '', ['rows' => 5, 'required' => true, 'class' => 'form-control'])
            ->addLabel('picture', 'Image (optionnelle)')
            ->addInput('file', 'picture', ['class' => 'form-control'])
            ->addInput('submit', 'submit', ['value' => 'Modifier', 'class' => 'mt-4 btn btn-primary'])
            ->endForm();

        $this->render('creation/edit', [
            'creation' => $creation,
            'form' => $form->getFormElements(),
            'error' => $error,
        ]);
    }
}
