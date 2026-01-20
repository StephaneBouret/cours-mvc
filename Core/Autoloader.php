<?php

declare(strict_types=1);

namespace App\Core;

final class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    private static function autoload(string $class): void
    {
        $prefix = 'App\\';

        // On ne charge que les classes de l'application
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        // Sécurité minimale
        if (str_contains($class, '..')) {
            return;
        }

        // On enlève "App\" car il n'existe pas de dossier App/
        // App\Controllers\HomeController -> Controllers\HomeController
        $relativeClass = substr($class, strlen($prefix));

        // Controllers\HomeController -> Controllers/HomeController.php
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        // Base dir = racine du projet (Core, Controllers, Models, Views...)
        $baseDir = dirname(__DIR__);

        $file = $baseDir . DIRECTORY_SEPARATOR . $relativePath;

        if (is_file($file)) {
            require_once $file;
        }
    }
}
