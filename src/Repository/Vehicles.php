<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Vehicle;
use DateTimeImmutable;
use PDO;
use RuntimeException;

final class VehicleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $data): Vehicle
    {
        return new Vehicle(
            internalReference: $data['internal_reference'],
            brand: $data['brand'],
            model: $data['model'],
            statusId: (int) $data['status_id'],
            vin: $data['vin'],
            trimLevel: $data['trim_level'],
            registrationNumber: $data['registration_number'],
            registrationYear: $data['registration_year'] === null
                ? null
                : (int) $data['registration_year'],
            mileageKm: $data['mileage_km'] === null
                ? null
                : (int) $data['mileage_km'],
            fuelType: $data['fuel_type'],
            transmission: $data['transmission'],
            powerHp: $data['power_hp'] === null
                ? null
                : (int) $data['power_hp'],
            exteriorColor: $data['exterior_color'],
            price: $data['price'] === null
                ? null
                : (float) $data['price'],
            description: $data['description'],
            isPublished: (bool) $data['is_published'],
            id: (int) $data['id'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at'])
        );
    }

    private function hydrateAll(array $rows): array
    {
        $vehicles = [];

        foreach ($rows as $row) {
            $vehicles[] = $this->hydrate($row);
        }

        return $vehicles;
    }
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM vehicles
             ORDER BY created_at DESC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllPublished(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM vehicles
             WHERE is_published = :is_published
             ORDER BY created_at DESC'
        );

        $stmt->execute([
            'is_published' => 1,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findById(int $id): ?Vehicle
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM vehicles
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findByInternalReference(string $internalReference): ?Vehicle
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM vehicles
             WHERE internal_reference = :internal_reference
             LIMIT 1'
        );

        $stmt->execute([
            'internal_reference' => $internalReference,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findByStatusId(int $statusId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM vehicles
             WHERE status_id = :status_id
             ORDER BY created_at DESC'
        );

        $stmt->execute([
            'status_id' => $statusId,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function save(Vehicle $vehicle): Vehicle
    {
        if ($vehicle->getId() === null) {
            return $this->create($vehicle);
        }

        return $this->update($vehicle);
    }

    private function create(Vehicle $vehicle): Vehicle
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO vehicles (
                status_id,
                internal_reference,
                vin,
                brand,
                model,
                trim_level,
                registration_number,
                registration_year,
                mileage_km,
                fuel_type,
                transmission,
                power_hp,
                exterior_color,
                price,
                description,
                is_published
            ) VALUES (
                :status_id,
                :internal_reference,
                :vin,
                :brand,
                :model,
                :trim_level,
                :registration_number,
                :registration_year,
                :mileage_km,
                :fuel_type,
                :transmission,
                :power_hp,
                :exterior_color,
                :price,
                :description,
                :is_published
            )'
        );

        $stmt->execute($this->getVehicleParameters($vehicle));

        $vehicleId = (int) $this->pdo->lastInsertId();
        $createdVehicle = $this->findById($vehicleId);

        if ($createdVehicle === null) {
            throw new RuntimeException('Le véhicule créé est introuvable.');
        }

        return $createdVehicle;
    }

    private function update(Vehicle $vehicle): Vehicle
    {
        $vehicleId = $vehicle->getId();

        if ($vehicleId === null) {
            throw new RuntimeException('Impossible de modifier un véhicule sans identifiant.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE vehicles SET
                status_id = :status_id,
                internal_reference = :internal_reference,
                vin = :vin,
                brand = :brand,
                model = :model,
                trim_level = :trim_level,
                registration_number = :registration_number,
                registration_year = :registration_year,
                mileage_km = :mileage_km,
                fuel_type = :fuel_type,
                transmission = :transmission,
                power_hp = :power_hp,
                exterior_color = :exterior_color,
                price = :price,
                description = :description,
                is_published = :is_published
             WHERE id = :id'
        );

        $parameters = $this->getVehicleParameters($vehicle);
        $parameters['id'] = $vehicleId;

        $stmt->execute($parameters);

        $updatedVehicle = $this->findById($vehicleId);

        if ($updatedVehicle === null) {
            throw new RuntimeException('Le véhicule modifié est introuvable.');
        }

        return $updatedVehicle;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM vehicles
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    private function getVehicleParameters(Vehicle $vehicle): array
    {
        return [
            'status_id' => $vehicle->getStatusId(),
            'internal_reference' => $vehicle->getInternalReference(),
            'vin' => $vehicle->getVin(),
            'brand' => $vehicle->getBrand(),
            'model' => $vehicle->getModel(),
            'trim_level' => $vehicle->getTrimLevel(),
            'registration_number' => $vehicle->getRegistrationNumber(),
            'registration_year' => $vehicle->getRegistrationYear(),
            'mileage_km' => $vehicle->getMileageKm(),
            'fuel_type' => $vehicle->getFuelType(),
            'transmission' => $vehicle->getTransmission(),
            'power_hp' => $vehicle->getPowerHp(),
            'exterior_color' => $vehicle->getExteriorColor(),
            'price' => $vehicle->getPrice(),
            'description' => $vehicle->getDescription(),
            'is_published' => $vehicle->isPublished() ? 1 : 0,
        ];
    }

}
