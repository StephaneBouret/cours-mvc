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
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Creation::createAndHydrate($row) : null;
    }

    public function insert(Creation $creation): Creation
    {
        $sql = 'INSERT INTO creation (title, description, created_at, picture) 
                VALUES (:title, :description, :created_at, :picture)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title'       => $creation->getTitle(),
            'description' => $creation->getDescription(),
            'created_at'  => $creation->getCreatedAt()->format('Y-m-d H:i:s'),
            'picture'     => $creation->getPicture(),
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $created = $this->find($id);
        if ($created === null) {
            throw new \RuntimeException('Insertion réussie mais relecture impossible.');
        }

        return $created;
    }

    public function update(Creation $creation): Creation
    {
        $sql = 'UPDATE creation 
                SET title = :title, 
                    description = :description, 
                    picture = :picture 
                WHERE id_creation = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'title' => $creation->getTitle(),
            'description' => $creation->getDescription(),
            'picture' => $creation->getPicture(),
            'id' => $creation->getIdCreation(),
        ]);

        return $this->find($creation->getIdCreation()) ?? throw new \RuntimeException('Mise à jour OK mais relecture impossible.');
    }

    public function delete(int $id): bool 
    {
        $sql = 'DELETE FROM creation WHERE id_creation = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
