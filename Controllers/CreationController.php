<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CreationModel;

final class CreationController extends Controller
{
    public function index(): void
    {
        $model = new CreationModel();
        $creations = $model->findAll();

        $this->render('creation/index', [
            'creations' => $creations
        ]);
    }
}