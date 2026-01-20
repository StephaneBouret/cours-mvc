<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Entities\Creation;

final class CreationModel extends Model
{
    /**
     * @return Creation[]
     */
    // public function findAll(): array
    // {
    //     $sql = 'SELECT id_creation, title, description, created_at, picture FROM creation';
    //     $stmt = $this->pdo->query($sql);

    //     $rows = $stmt->fetchAll(); // FETCH_ASSOC par défaut dans DbConnect (si tu l'as mis)
    //     $creations = [];

    //     foreach ($rows as $row) {
    //         $creations[] = Creation::createAndHydrate($row);
    //     }

    //     return $creations;
    // }
    public function findAll(): array
    {
        $sql = 'SELECT id_creation, title, description, created_at, picture
            FROM creation
            ORDER BY title ASC';
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(
            fn(array $row) => Creation::createAndHydrate($row),
            $rows
        );
    }

    public function find(int $id): ?Creation
    {
        $sql = 'SELECT id_creation, title, description, created_at, picture
FROM creation WHERE id_creation = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Creation::createAndHydrate($row) : null;
    }
}
