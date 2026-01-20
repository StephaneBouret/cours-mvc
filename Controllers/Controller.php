<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class Controller
{
    protected function render(string $path, array $data = []): void
    {
        // Transforme les clés du tableau en variables
        extract($data);
        
        // Inclut le fichier de la vue
        require dirname(__DIR__) . "/Views/{$path}.php";
    }
}
