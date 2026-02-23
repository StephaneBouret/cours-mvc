<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\Autoloader;

// Chargement de l'autoloader
require_once dirname(__DIR__) . '/Core/Autoloader.php';

session_start();

// Enregistrement de l'autoloader (méthode statique)
Autoloader::register();

// Lancement de l'application
$route = new Router();
$route->routes();
