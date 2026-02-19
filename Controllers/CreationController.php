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

        $form = new Form();
        $form
            ->startForm('#', 'POST', ['enctype' => 'multipart/form-data'])
            ->addLabel('title', 'Titre')
            ->addInput('text', 'title', ['id' => 'title', 'required' => true, 'class' => 'form-control'])

            ->addLabel('description', 'Description')
            ->addTextarea('description', '', ['rows' => 5, 'id' => 'description', 'class' => 'form-control'])

            ->addLabel('picture', 'Image')
            ->addInput('file', 'picture', ['class' => 'form-control'])

            ->addInput('submit', 'submit', ['value' => 'Ajouter', 'class' => 'mt-5 btn btn-primary'])
            ->endForm();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('creation/add', [
                'form' => $form->getFormElements(),
                'error' => $error,
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Form::validatePost($_POST, ['title', 'description'])) {
                $error = 'Tous les champs sont obligatoires';
            } elseif (!Form::validateFiles($_FILES, ['picture'])) {
                $error = 'Image invalide';
            } else {
                $filename = uniqid() . '_' . $_FILES['picture']['name'];
                move_uploaded_file(
                    $_FILES['picture']['tmp_name'],
                    dirname(__DIR__) . '/public/uploads/' . $filename
                );

                // Création et hydratation de l'entité
                $creation = new Creation();
                $creation->setTitle((string) trim($_POST['title']));
                $creation->setDescription((string) trim($_POST['description']));
                $creation->setPicture($filename);

                // Délégation au Model
                $model = new CreationModel();
                $created = $model->insert($creation);

                header(
                    // 'Location: index.php?controller=creation&action=show&id='
                    //     . $created->getIdCreation()
                    'Location: index.php?controller=creation&action=index'
                );
                exit;
            }
        }

        $this->render('creation/add', [
            'form' => $form->getFormElements(),
            'error' => $error,
        ]);
    }
}
