<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

final class Vehicle
{
    private ?int $id;
    private int $statusId;
    private string $internalReference;
    private ?string $vin;
    private string $brand;
    private string $model;
    private ?string $trimLevel;
    private ?string $registrationNumber;
    private ?int $registrationYear;
    private ?int $mileageKm;
    private ?string $fuelType;
    private ?string $transmission;
    private ?int $powerHp;
    private ?string $exteriorColor;
    private ?float $price;
    private ?string $description;
    private bool $isPublished;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $internalReference,
        string $brand,
        string $model,
        int $statusId = 1,
        ?string $vin = null,
        ?string $trimLevel = null,
        ?string $registrationNumber = null,
        ?int $registrationYear = null,
        ?int $mileageKm = null,
        ?string $fuelType = null,
        ?string $transmission = null,
        ?int $powerHp = null,
        ?string $exteriorColor = null,
        ?float $price = null,
        ?string $description = null,
        bool $isPublished = false,
        ?int $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->setStatusId($statusId);
        $this->setInternalReference($internalReference);
        $this->setVin($vin);
        $this->setBrand($brand);
        $this->setModel($model);
        $this->trimLevel = self::normalizeNullableString($trimLevel);
        $this->setRegistrationNumber($registrationNumber);
        $this->setRegistrationYear($registrationYear);
        $this->setMileageKm($mileageKm);
        $this->fuelType = self::normalizeNullableString($fuelType);
        $this->transmission = self::normalizeNullableString($transmission);
        $this->setPowerHp($powerHp);
        $this->exteriorColor = self::normalizeNullableString($exteriorColor);
        $this->setPrice($price);
        $this->description = self::normalizeNullableString($description);
        $this->isPublished = $isPublished;
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

    public function setId(int $id): self
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('L’identifiant doit être supérieur à zéro.');
        }

        $this->id = $id;

        return $this;
    }

    public function setStatusId(int $statusId): self
    {
        if ($statusId <= 0) {
            throw new InvalidArgumentException('Le statut du véhicule est invalide.');
        }

        $this->statusId = $statusId;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setInternalReference(string $internalReference): self
    {
        $internalReference = trim($internalReference);

        if ($internalReference === '') {
            throw new InvalidArgumentException('La référence interne est obligatoire.');
        }

        $this->internalReference = $internalReference;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setVin(?string $vin): self
    {
        $vin = self::normalizeNullableString($vin);

        if ($vin !== null && strlen($vin) > 17) {
            throw new InvalidArgumentException('Le numéro VIN ne peut pas dépasser 17 caractères.');
        }

        $this->vin = $vin === null ? null : strtoupper($vin);

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setBrand(string $brand): self
    {
        $brand = trim($brand);

        if ($brand === '') {
            throw new InvalidArgumentException('La marque du véhicule est obligatoire.');
        }

        $this->brand = $brand;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setModel(string $model): self
    {
        $model = trim($model);

        if ($model === '') {
            throw new InvalidArgumentException('Le modèle du véhicule est obligatoire.');
        }

        $this->model = $model;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setTrimLevel(?string $trimLevel): self
    {
        $this->trimLevel = self::normalizeNullableString($trimLevel);
        $this->touch();

        return $this;
    }

    public function setRegistrationNumber(?string $registrationNumber): self
    {
        $registrationNumber = self::normalizeNullableString($registrationNumber);
        $this->registrationNumber = $registrationNumber === null
            ? null
            : strtoupper($registrationNumber);

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setRegistrationYear(?int $registrationYear): self
    {
        $maximumYear = (int) date('Y') + 1;

        if (
            $registrationYear !== null
            && ($registrationYear < 1886 || $registrationYear > $maximumYear)
        ) {
            throw new InvalidArgumentException('L’année du véhicule est invalide.');
        }

        $this->registrationYear = $registrationYear;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setMileageKm(?int $mileageKm): self
    {
        if ($mileageKm !== null && $mileageKm < 0) {
            throw new InvalidArgumentException('Le kilométrage ne peut pas être négatif.');
        }

        $this->mileageKm = $mileageKm;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setFuelType(?string $fuelType): self
    {
        $this->fuelType = self::normalizeNullableString($fuelType);
        $this->touch();

        return $this;
    }

    public function setTransmission(?string $transmission): self
    {
        $this->transmission = self::normalizeNullableString($transmission);
        $this->touch();

        return $this;
    }

    public function setPowerHp(?int $powerHp): self
    {
        if ($powerHp !== null && $powerHp <= 0) {
            throw new InvalidArgumentException('La puissance doit être supérieure à zéro.');
        }

        $this->powerHp = $powerHp;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setExteriorColor(?string $exteriorColor): self
    {
        $this->exteriorColor = self::normalizeNullableString($exteriorColor);
        $this->touch();

        return $this;
    }

    public function setPrice(?float $price): self
    {
        if ($price !== null && $price < 0) {
            throw new InvalidArgumentException('Le prix ne peut pas être négatif.');
        }

        $this->price = $price;

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

    public function getId(): ?int                            {return $this->id;}

    public function getStatusId(): int                       {return $this->statusId;}

    public function getInternalReference(): string           {return $this->internalReference;}

    public function getVin(): ?string                        {return $this->vin;}

    public function getBrand(): string                       {return $this->brand;}

    public function getModel(): string                       {return $this->model;}

    public function getTrimLevel(): ?string                  {return $this->trimLevel;}

    public function getRegistrationNumber(): ?string         {return $this->registrationNumber;}

    public function getRegistrationYear(): ?int              {return $this->registrationYear;}

    public function getMileageKm(): ?int                     {return $this->mileageKm;}

    public function getFuelType(): ?string                   {return $this->fuelType;}

    public function getTransmission(): ?string               {return $this->transmission;}

    public function getPowerHp(): ?int                       {return $this->powerHp;}

    public function getExteriorColor(): ?string              {return $this->exteriorColor;}

    public function getPrice(): ?float                       {return $this->price;}

    public function getDescription(): ?string                {return $this->description;}

    public function isPublished(): bool                      {return $this->isPublished;}

    public function publish(): self                          {$this->isPublished = true; $this->touch(); return $this;}

    public function unpublish(): self                        {$this->isPublished = false; $this->touch(); return $this;}

    public function getCreatedAt(): DateTimeImmutable        {return $this->createdAt;}

    public function getUpdatedAt(): DateTimeImmutable        {return $this->updatedAt;}

    private function touch(): void                           {$this->updatedAt = new DateTimeImmutable();}
}
