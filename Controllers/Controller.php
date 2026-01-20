<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class Controller
{
    protected function render(string $path, array $data = [], string $layout = 'layout/base'): void
    {
        // 1) On transforme le tableau en variables
        // EXTR_SKIP évite d'écraser des variables internes comme $content, $pageTitle...
        extract($data, EXTR_SKIP);
        
        // 2) Buffer de la vue : on capture le HTML produit par la vue
        ob_start();
        require dirname(__DIR__) . "/Views/{$path}.php";
        $content = ob_get_clean();
        
        // 3) Layout : il va utiliser $content (+ éventuellement $pageTitle, ...)
        require dirname(__DIR__) . "/Views/{$layout}.php";
    }
}
