<?php

declare(strict_types=1);

namespace App\Controllers;

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

    public function add(): void
    {
        $error = null;
        // ✅ IMPORTANT : la Form impose une session déjà démarrée (sinon exception)
        // => normalement c'est dans public/index.php : session_start();
        // Ici on ne force pas session_start() pour rester cohérent avec le message d'erreur.
        // 1) Traiter le POST AVANT de reconstruire le form (sinon le CSRF se fait écraser)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Form::isCsrfTokenValid('creation_add', $_POST['_csrf'] ?? null)) {
                $error = 'CSRF invalide.';
            } elseif (!Form::validatePost($_POST, ['title', 'description', 'category'])) {
                $error = 'Champs manquants.';
            } elseif (!Form::validateFiles($_FILES, ['picture'])) {
                $error = 'Image invalide.';
            } else {
                $filename = uniqid() . '_' . basename((string) ($_FILES['picture']['name'] ?? 'image'));
                move_uploaded_file(
                    (string) $_FILES['picture']['tmp_name'],
                    dirname(__DIR__) . '/public/uploads/' . $filename
                );
                // Création et hydratation de l'entité
                $creation = new Creation();
                $creation->setTitle((string) trim($_POST['title']));
                $creation->setDescription((string) trim($_POST['description']));
                $creation->setPicture($filename);
                // Délégation au Model
                $model = new CreationModel();
                $model->insert($creation);
                header('Location: index.php?controller=creation&action=index');
                // 'Location: index.php?controller=creation&action=show&id='
                // . $created->getIdCreation()
                exit;
            }
        }
        // 2) Construire le formulaire (GET ou POST avec erreurs)
        $form = new Form($_POST);
        $form
            ->enableCsrf('creation_add') // IMPORTANT : génère + injecte le token
            ->startForm('#', 'POST', ['enctype' => 'multipart/form-data'])
            ->addLabel('title', 'Titre')
            ->addInput('text', 'title', ['id' => 'title', 'required' => true, 'class' => 'form-control'])
            ->addLabel('description', 'Description')
            ->addTextarea('description', '', ['rows' => 5, 'id' => 'description', 'class' => 'form-control'])
            ->addLabel('category', 'Catégorie')
            ->addSelect('category', [
                'peinture' => 'Peinture',
                'photo' => 'Photo',
                'sculpture' => 'Sculpture',
            ], ['class' => 'form-control'])
            ->addLabel('picture', 'Image')
            ->addInput('file', 'picture', ['class' => 'form-control'])
            ->addInput('submit', 'submit', ['value' => 'Ajouter', 'class' => 'mt-5 btn btn-primary'])
            ->endForm();
        $this->render('creation/add', [
            'form' => $form->getFormElements(),
            'error' => $error,
        ]);
    }
}
