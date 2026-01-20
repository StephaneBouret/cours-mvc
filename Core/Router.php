<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    public function routes(): void
    {
        // 1) Lire controller/action depuis l'URL (ou valeurs par défaut)
        $controller = (string)($_GET['controller'] ?? 'home');
        $action = (string)($_GET['action'] ?? 'index');

        // 2) Ne pas transmettre controller/action au contrôleur
        // le contrôleur n'a pas à savoir comment l'URL est construite
        unset($_GET['controller'], $_GET['action']);

        // 3) Construire le nom complet de la classe contrôleur
        // Ex: "home" => "App\Controllers\HomeController"
        $controllerClass = 'App\\Controllers\\' . ucfirst($controller) . 'Controller';
        $method          = $action;

        // 4) Vérifier l'existence du contrôleur + de la méthode
        if (!class_exists($controllerClass)) {
            $this->notFound("Contrôleur introuvable : $controllerClass");
            return;
        }

        $controllerObject = new $controllerClass();

        if (!method_exists($controllerObject, $method)) {
            $this->notFound("Action introuvable : {$controllerClass}::{$method}()");
            return;
        }

        // 5) Paramètres restants (ex: id=12, slug=hello)
        // On ne garde que les valeurs (et pas les clés) pour les passer à la méthode
        // Ex: ['id' => 12] devient 12
        $params = array_values($_GET);

        // 6) Appel dynamique :
        // - Si on a des paramètres → call_user_func_array
        // - Sinon → appel direct
        if (!empty($params)) {
            call_user_func_array([$controllerObject, $method], $params);
            return;
        }

        $controllerObject->$method();
    }

    private function notFound(string $debugMessage = ''): void
    {
        http_response_code(404);
        echo '404 - Page introuvable';
    }
}
