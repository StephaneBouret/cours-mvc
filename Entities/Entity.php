<?php

declare(strict_types=1);

namespace App\Entities;

use DateTimeImmutable;

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

    /**
     * Hydrate les propriétés à partir d'un tableau associatif (PDO::FETCH_ASSOC)
     */
    protected function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            // Génère le nom du setter : created_at -> setCreatedAt
            $method = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));

            // Si le setter n'existe pas, on ignore la colonne
            if (!method_exists($this, $method)) {
                continue;
            }
            /**
             * Cas spécial : PDO renvoie les dates en string
             * On convertit pour travailler avec un vrai type date dans l'entité.
             *
             * Colonnes ciblées (niveau 1) : created_at, updated_at, deleted_at
             */
            if (
                in_array($key, ['created_at', 'updated_at', 'deleted_at'], true)
                && is_string($value)
                && $value !== ''
            ) {
                $value = new DateTimeImmutable($value);
            }

            // Appel dynamique du setter
            $this->$method($value);
        }
    }
}
