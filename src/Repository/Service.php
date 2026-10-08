<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Service;
use DateTimeImmutable;
use PDO;
use RuntimeException;

final class ServiceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $data): Service
    {
        return new Service(
            name: $data['name'],
            slug: $data['slug'],
            regularPrice: (float) $data['regular_price'],
            description: $data['description'],
            promotionalPrice: $data['promotional_price'] === null
                ? null
                : (float) $data['promotional_price'],
            imagePath: $data['image_path'],
            isActive: (bool) $data['is_active'],
            displayOrder: (int) $data['display_order'],
            id: (int) $data['id'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at'])
        );
    }

    private function hydrateAll(array $rows): array
    {
        $services = [];

        foreach ($rows as $row) {
            $services[] = $this->hydrate($row);
        }

        return $services;
    }

    private function getServiceParameters(Service $service): array
    {
        return [
            'name' => $service->getName(),
            'slug' => $service->getSlug(),
            'description' => $service->getDescription(),
            'regular_price' => $service->getRegularPrice(),
            'promotional_price' => $service->getPromotionalPrice(),
            'image_path' => $service->getImagePath(),
            'is_active' => $service->isActive() ? 1 : 0,
            'display_order' => $service->getDisplayOrder(),
        ];
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM services
             ORDER BY display_order ASC, name ASC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM services
             WHERE is_active = :is_active
             ORDER BY display_order ASC, name ASC'
        );

        $stmt->execute([
            'is_active' => 1,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findById(int $id): ?Service
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM services
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findBySlug(string $slug): ?Service
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM services
             WHERE slug = :slug
             LIMIT 1'
        );

        $stmt->execute([
            'slug' => $slug,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findActiveBySlug(string $slug): ?Service
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM services
             WHERE slug = :slug
               AND is_active = :is_active
             LIMIT 1'
        );

        $stmt->execute([
            'slug' => $slug,
            'is_active' => 1,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function save(Service $service): Service
    {
        if ($service->getId() === null) {
            return $this->create($service);
        }

        return $this->update($service);
    }

    private function create(Service $service): Service
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO services (
                name,
                slug,
                description,
                regular_price,
                promotional_price,
                image_path,
                is_active,
                display_order
            ) VALUES (
                :name,
                :slug,
                :description,
                :regular_price,
                :promotional_price,
                :image_path,
                :is_active,
                :display_order
            )'
        );

        $stmt->execute($this->getServiceParameters($service));

        $serviceId = (int) $this->pdo->lastInsertId();
        $createdService = $this->findById($serviceId);

        if ($createdService === null) {
            throw new RuntimeException('Le service créé est introuvable.');
        }

        return $createdService;
    }

    private function update(Service $service): Service
    {
        $serviceId = $service->getId();

        if ($serviceId === null) {
            throw new RuntimeException('Impossible de modifier un service sans identifiant.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE services SET
                name = :name,
                slug = :slug,
                description = :description,
                regular_price = :regular_price,
                promotional_price = :promotional_price,
                image_path = :image_path,
                is_active = :is_active,
                display_order = :display_order
             WHERE id = :id'
        );

        $parameters = $this->getServiceParameters($service);
        $parameters['id'] = $serviceId;

        $stmt->execute($parameters);

        $updatedService = $this->findById($serviceId);

        if ($updatedService === null) {
            throw new RuntimeException('Le service modifié est introuvable.');
        }

        return $updatedService;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM services
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }
}
