<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    public function routes(): void
    {
        // TODO 1 : lire controller / action depuis l'URL (valeurs par défaut)
        $controller = (string) ($_GET['controller'] ?? 'home');
        $action = (string) ($_GET['action'] ?? 'index');

        // TODO 2 : retirer controller/action de $_GET
        unset($_GET['controller'], $_GET['action']);

        // TODO 9 : sécurité minimale sur controller/action (caractères autorisés)
        // - autoriser uniquement lettres a-z (minuscules), chiffres, underscore
        // - sinon -> notFound()
        if (!$this->isSafeToken($controller) || !$this->isSafeToken($action)) {
            $this->notFound('controller/action non valides');
            return;
        }

        // TODO 3 : construire le nom de classe complet
        // Ex: home => App\Controllers\HomeController
        $controllerClass = 'App\\Controllers\\' . ucfirst($controller) . 'Controller';
        $method = $action;

        // TODO 4 : vérifier que le contrôleur existe
        if (!class_exists($controllerClass)) {
            $this->notFound("Contrôleur introuvable : $controllerClass");
            return;
        }

        $controllerObject = new $controllerClass();

        // TODO 5 : vérifier que l'action existe
        if (!method_exists($controllerObject, $method)) {
            $this->notFound("Action introuvable : {$controllerClass}::{$method}()");
            return;
        }

        // TODO 10 : typage minimal du param "id" si présent dans l'URL
        // - si $_GET contient 'id', il doit être numérique
        // - et on remplace sa valeur par (int) $_GET['id']
        // - sinon -> notFound()
        if (isset($_GET['id'])) {
            $rawId = (string) $_GET['id'];
            if (!ctype_digit($rawId)) {
                $this->notFound('Paramètre id invalide');
                return;
            }
            $_GET['id'] = (int) $rawId;
        }

        // TODO 6 : récupérer les paramètres restants (ex: id=12)
        $params = array_values($_GET);


        // TODO 7 : appeler la méthode (avec ou sans paramètres)
        if (!empty($params)) {
            call_user_func_array([$controllerObject, $method], $params);
            return;
        }
        $controllerObject->$method();
    }

    // TODO 8 : méthode de sécurité minimale (token safe)
    private function isSafeToken(string $value): bool 
    {
        return (bool) preg_match('/^[a-z0-9_]+$/', $value);
    }

    private function notFound(string $debugMessage = ''): void
    {
        http_response_code(404);
        echo '404 - Page introuvable';
    }
}
