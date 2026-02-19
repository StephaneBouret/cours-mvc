<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Entity;
use DateTimeImmutable;

final class Creation extends Entity
{
    private int $idCreation;
    private string $title;
    private string $description;
    private DateTimeImmutable $createdAt;
    private ?string $picture = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getIdCreation(): int
    {
        return $this->idCreation;
    }

    public function setIdCreation(int $id): void
    {
        $this->idCreation = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable|string $createdAt): void
    {
        if (is_string($createdAt)) {
            $createdAt = new DateTimeImmutable($createdAt);
        }
        $this->createdAt = $createdAt;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): void
    {
        $this->picture = $picture !== null && $picture !== ''
            ? trim($picture)
            : null;
    }
}
