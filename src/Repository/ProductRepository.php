<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use DateTimeImmutable;
use PDO;
use RuntimeException;

final class ProductRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $data): Product
    {
        return new Product(
            name: $data['name'],
            slug: $data['slug'],
            regularPrice: (float) $data['regular_price'],
            sku: $data['sku'],
            description: $data['description'],
            promotionalPrice: $data['promotional_price'] === null
                ? null
                : (float) $data['promotional_price'],
            stockQuantity: (int) $data['stock_quantity'],
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
        $products = [];

        foreach ($rows as $row) {
            $products[] = $this->hydrate($row);
        }

        return $products;
    }

    private function getProductParameters(Product $product): array
    {
        return [
            'name' => $product->getName(),
            'slug' => $product->getSlug(),
            'sku' => $product->getSku(),
            'description' => $product->getDescription(),
            'regular_price' => $product->getRegularPrice(),
            'promotional_price' => $product->getPromotionalPrice(),
            'stock_quantity' => $product->getStockQuantity(),
            'image_path' => $product->getImagePath(),
            'is_active' => $product->isActive() ? 1 : 0,
            'display_order' => $product->getDisplayOrder(),
        ];
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM products
             ORDER BY display_order ASC, name ASC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM products
             WHERE is_active = :is_active
             ORDER BY display_order ASC, name ASC'
        );

        $stmt->execute([
            'is_active' => 1,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM products
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findBySlug(string $slug): ?Product
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM products
             WHERE slug = :slug
             LIMIT 1'
        );

        $stmt->execute([
            'slug' => $slug,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findActiveBySlug(string $slug): ?Product
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM products
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

    public function findBySku(string $sku): ?Product
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM products
             WHERE sku = :sku
             LIMIT 1'
        );

        $stmt->execute([
            'sku' => strtoupper(trim($sku)),
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function save(Product $product): Product
    {
        if ($product->getId() === null) {
            return $this->create($product);
        }

        return $this->update($product);
    }

    private function create(Product $product): Product
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (
                name,
                slug,
                sku,
                description,
                regular_price,
                promotional_price,
                stock_quantity,
                image_path,
                is_active,
                display_order
            ) VALUES (
                :name,
                :slug,
                :sku,
                :description,
                :regular_price,
                :promotional_price,
                :stock_quantity,
                :image_path,
                :is_active,
                :display_order
            )'
        );

        $stmt->execute($this->getProductParameters($product));

        $productId = (int) $this->pdo->lastInsertId();
        $createdProduct = $this->findById($productId);

        if ($createdProduct === null) {
            throw new RuntimeException('Le produit créé est introuvable.');
        }

        return $createdProduct;
    }

    private function update(Product $product): Product
    {
        $productId = $product->getId();

        if ($productId === null) {
            throw new RuntimeException('Impossible de modifier un produit sans identifiant.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE products SET
                name = :name,
                slug = :slug,
                sku = :sku,
                description = :description,
                regular_price = :regular_price,
                promotional_price = :promotional_price,
                stock_quantity = :stock_quantity,
                image_path = :image_path,
                is_active = :is_active,
                display_order = :display_order
             WHERE id = :id'
        );

        $parameters = $this->getProductParameters($product);
        $parameters['id'] = $productId;

        $stmt->execute($parameters);

        $updatedProduct = $this->findById($productId);

        if ($updatedProduct === null) {
            throw new RuntimeException('Le produit modifié est introuvable.');
        }

        return $updatedProduct;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM products
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }
}
