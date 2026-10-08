<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

final class Product
{
    private ?int $id;
    private string $name;
    private string $slug;
    private ?string $sku;
    private ?string $description;
    private float $regularPrice;
    private ?float $promotionalPrice;
    private int $stockQuantity;
    private ?string $imagePath;
    private bool $isActive;
    private int $displayOrder;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $name,
        string $slug,
        float $regularPrice,
        ?string $sku = null,
        ?string $description = null,
        ?float $promotionalPrice = null,
        int $stockQuantity = 0,
        ?string $imagePath = null,
        bool $isActive = true,
        int $displayOrder = 0,
        ?int $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->setName($name);
        $this->setSlug($slug);
        $this->setSku($sku);
        $this->description = self::normalizeNullableString($description);
        $this->setRegularPrice($regularPrice);
        $this->setPromotionalPrice($promotionalPrice);
        $this->setStockQuantity($stockQuantity);
        $this->imagePath = self::normalizeNullableString($imagePath);
        $this->isActive = $isActive;
        $this->setDisplayOrder($displayOrder);
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    private static function normalizeNullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    public function setName(string $name): self
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Le nom du produit est obligatoire.');
        }

        $this->name = $name;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setSlug(string $slug): self
    {
        $slug = strtolower(trim($slug));

        if ($slug === '') {
            throw new InvalidArgumentException('Le slug du produit est obligatoire.');
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new InvalidArgumentException('Le slug du produit est invalide.');
        }

        $this->slug = $slug;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setSku(?string $sku): self
    {
        $sku = self::normalizeNullableString($sku);
        $this->sku = $sku === null ? null : strtoupper($sku);

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = self::normalizeNullableString($description);
        $this->touch();

        return $this;
    }

    public function setRegularPrice(float $regularPrice): self
    {
        if ($regularPrice < 0) {
            throw new InvalidArgumentException('Le prix du produit ne peut pas être négatif.');
        }

        $this->regularPrice = $regularPrice;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setPromotionalPrice(?float $promotionalPrice): self
    {
        if ($promotionalPrice !== null && $promotionalPrice < 0) {
            throw new InvalidArgumentException('Le prix promotionnel ne peut pas être négatif.');
        }

        $this->promotionalPrice = $promotionalPrice;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setStockQuantity(int $stockQuantity): self
    {
        if ($stockQuantity < 0) {
            throw new InvalidArgumentException('Le stock ne peut pas être négatif.');
        }

        $this->stockQuantity = $stockQuantity;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function increaseStock(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La quantité ajoutée doit être supérieure à zéro.');
        }

        $this->stockQuantity += $quantity;
        $this->touch();

        return $this;
    }

    public function decreaseStock(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('La quantité retirée doit être supérieure à zéro.');
        }

        if ($quantity > $this->stockQuantity) {
            throw new InvalidArgumentException('Le stock disponible est insuffisant.');
        }

        $this->stockQuantity -= $quantity;
        $this->touch();

        return $this;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = self::normalizeNullableString($imagePath);
        $this->touch();

        return $this;
    }

    public function setDisplayOrder(int $displayOrder): self
    {
        if ($displayOrder < 0) {
            throw new InvalidArgumentException('L’ordre d’affichage ne peut pas être négatif.');
        }

        $this->displayOrder = $displayOrder;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function getId(): ?int                            {return $this->id;}

    public function getName(): string                        {return $this->name;}

    public function getSlug(): string                        {return $this->slug;}

    public function getSku(): ?string                        {return $this->sku;}

    public function getDescription(): ?string                {return $this->description;}

    public function getRegularPrice(): float                 {return $this->regularPrice;}

    public function getPromotionalPrice(): ?float            {return $this->promotionalPrice;}

    public function getCurrentPrice(): float                 {return $this->promotionalPrice ?? $this->regularPrice;}

    public function getStockQuantity(): int                  {return $this->stockQuantity;}

    public function isInStock(): bool                        {return $this->stockQuantity > 0;}

    public function getImagePath(): ?string                  {return $this->imagePath;}

    public function isActive(): bool                         {return $this->isActive;}

    public function activate(): self                         {$this->isActive = true; $this->touch(); return $this;}

    public function deactivate(): self                       {$this->isActive = false; $this->touch(); return $this;}

    public function getDisplayOrder(): int                   {return $this->displayOrder;}

    public function getCreatedAt(): DateTimeImmutable        {return $this->createdAt;}

    public function getUpdatedAt(): DateTimeImmutable        {return $this->updatedAt;}

    private function touch(): void                           {$this->updatedAt = new DateTimeImmutable();}
}
