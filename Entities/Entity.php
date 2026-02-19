<?php

declare(strict_types=1);

namespace App\Entities;

abstract class Entity
{
    /**
     * Crée une instance de l'entité et l'hydrate
     */
    public static function createAndHydrate(array $data): static
    {
        $entity = new static();
        $entity->hydrate($data);
        return $entity;
    }

    protected function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            // Génère le nom du setter : created_at -> setCreatedAt
            $method = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));

            // Si le setter n'existe pas, on ignore la colonne
            if (!method_exists($this, $method)) {
                continue;
            }
            // Appel dynamique du setter
            $this->$method($value);
        }
    }
}
