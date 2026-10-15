<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;
use RuntimeException;

final class AdminContentRepository
{
    private const ALLOWED_TABLES = [
        'vehicles',
        'vehicle_images',
        'vehicle_statuses',
        'products',
        'services',
        'contact_requests',
        'opening_hours',
    ];

    private array $columnsCache = [];

    public function __construct(private PDO $pdo)
    {
    }

    public function findAll(string $table): array
    {
        $this->assertTable($table);

        if (!$this->tableExists($table)) {
            return [];
        }

        $order = $this->columnExists($table, 'display_order')
            ? 'display_order ASC, id DESC'
            : 'id DESC';

        return $this->pdo
            ->query("SELECT * FROM `{$table}` ORDER BY {$order}")
            ->fetchAll();
    }

    public function findById(string $table, int $id): ?array
    {
        $this->assertTable($table);

        if ($id <= 0 || !$this->tableExists($table)) {
            return null;
        }

        $stmt = $this->pdo->prepare(
            "SELECT * FROM `{$table}` WHERE id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function save(string $table, array $data, ?int $id = null): int
    {
        $this->assertTable($table);

        if (!$this->tableExists($table)) {
            throw new RuntimeException("La table {$table} n’existe pas.");
        }

        $data = $this->filterData($table, $data);

        if ($data === []) {
            throw new InvalidArgumentException('Aucune donnée valide à enregistrer.');
        }

        if ($id === null) {
            $columns = array_keys($data);
            $columnSql = implode(', ', array_map(
                static fn (string $column): string => "`{$column}`",
                $columns
            ));
            $placeholderSql = implode(', ', array_map(
                static fn (string $column): string => ':' . $column,
                $columns
            ));

            $stmt = $this->pdo->prepare(
                "INSERT INTO `{$table}` ({$columnSql}) VALUES ({$placeholderSql})"
            );
            $stmt->execute($data);

            return (int) $this->pdo->lastInsertId();
        }

        if ($id <= 0) {
            throw new InvalidArgumentException('Identifiant invalide.');
        }

        $assignments = implode(', ', array_map(
            static fn (string $column): string => "`{$column}` = :{$column}",
            array_keys($data)
        ));
        $data['id'] = $id;

        $stmt = $this->pdo->prepare(
            "UPDATE `{$table}` SET {$assignments} WHERE id = :id"
        );
        $stmt->execute($data);

        return $id;
    }

    public function delete(string $table, int $id): bool
    {
        $this->assertTable($table);

        if ($id <= 0 || !$this->tableExists($table)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM `{$table}` WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function findVehicleStatuses(): array
    {
        if (!$this->tableExists('vehicle_statuses')) {
            return [
                ['id' => 1, 'label' => 'Disponible à l’achat'],
                ['id' => 2, 'label' => 'En homologation'],
                ['id' => 3, 'label' => 'Service programmé'],
                ['id' => 4, 'label' => 'Véhicule rendu'],
            ];
        }

        $labelColumn = match (true) {
            $this->columnExists('vehicle_statuses', 'label') => 'label',
            $this->columnExists('vehicle_statuses', 'name') => 'name',
            $this->columnExists('vehicle_statuses', 'status_name') => 'status_name',
            $this->columnExists('vehicle_statuses', 'code') => 'code',
            default => 'id',
        };

        return $this->pdo
            ->query("SELECT id, `{$labelColumn}` AS label FROM vehicle_statuses ORDER BY id")
            ->fetchAll();
    }

    public function findVehicleImages(int $vehicleId): array
    {
        if ($vehicleId <= 0 || !$this->tableExists('vehicle_images')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, vehicle_id, file_path, alt_text, is_cover, display_order
             FROM vehicle_images
             WHERE vehicle_id = :vehicle_id
             ORDER BY is_cover DESC, display_order ASC, id ASC'
        );
        $stmt->execute(['vehicle_id' => $vehicleId]);

        return $stmt->fetchAll();
    }

    public function addVehicleImage(
        int $vehicleId,
        string $filePath,
        ?string $altText,
        bool $isCover
    ): int {
        if (!$this->tableExists('vehicle_images')) {
            throw new RuntimeException('La table vehicle_images n’existe pas.');
        }

        if ($isCover) {
            $this->clearVehicleCover($vehicleId);
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO vehicle_images (
                vehicle_id, file_path, alt_text, is_cover, display_order
             ) VALUES (
                :vehicle_id, :file_path, :alt_text, :is_cover,
                COALESCE((
                    SELECT next_order
                    FROM (
                        SELECT MAX(display_order) + 1 AS next_order
                        FROM vehicle_images
                        WHERE vehicle_id = :order_vehicle_id
                    ) AS image_order
                ), 0)
             )'
        );
        $stmt->execute([
            'vehicle_id' => $vehicleId,
            'file_path' => $filePath,
            'alt_text' => $altText,
            'is_cover' => $isCover ? 1 : 0,
            'order_vehicle_id' => $vehicleId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function setVehicleCover(int $vehicleId, int $imageId): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->clearVehicleCover($vehicleId);
            $stmt = $this->pdo->prepare(
                'UPDATE vehicle_images
                 SET is_cover = 1
                 WHERE id = :id AND vehicle_id = :vehicle_id'
            );
            $stmt->execute(['id' => $imageId, 'vehicle_id' => $vehicleId]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Image introuvable.');
            }

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function deleteVehicleImage(int $vehicleId, int $imageId): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT file_path, is_cover
             FROM vehicle_images
             WHERE id = :id AND vehicle_id = :vehicle_id
             LIMIT 1'
        );
        $stmt->execute(['id' => $imageId, 'vehicle_id' => $vehicleId]);
        $image = $stmt->fetch();

        if ($image === false) {
            return null;
        }

        $delete = $this->pdo->prepare(
            'DELETE FROM vehicle_images
             WHERE id = :id AND vehicle_id = :vehicle_id'
        );
        $delete->execute(['id' => $imageId, 'vehicle_id' => $vehicleId]);

        if ((bool) $image['is_cover']) {
            $nextCover = $this->pdo->prepare(
                'UPDATE vehicle_images
                 SET is_cover = 1
                 WHERE vehicle_id = :vehicle_id
                 ORDER BY display_order ASC, id ASC
                 LIMIT 1'
            );
            $nextCover->execute(['vehicle_id' => $vehicleId]);
        }

        return (string) $image['file_path'];
    }

    public function deleteVehicleWithImages(int $vehicleId): array
    {
        $images = $this->findVehicleImages($vehicleId);
        $paths = array_column($images, 'file_path');

        $this->pdo->beginTransaction();

        try {
            if ($this->tableExists('vehicle_images')) {
                $stmt = $this->pdo->prepare(
                    'DELETE FROM vehicle_images WHERE vehicle_id = :vehicle_id'
                );
                $stmt->execute(['vehicle_id' => $vehicleId]);
            }

            $this->delete('vehicles', $vehicleId);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $paths;
    }

    public function findOpeningHours(): array
    {
        if (!$this->tableExists('opening_hours')) {
            return [];
        }

        return $this->pdo
            ->query('SELECT * FROM opening_hours ORDER BY day_number ASC')
            ->fetchAll();
    }

    public function updateContactStatus(int $id, string $status): void
    {
        $allowedStatuses = [
            'NEW',
            'IN_PROGRESS',
            'CLOSED',
            'ARCHIVED',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException(
                'Statut de contact invalide.'
            );
        }
        if (!$this->columnExists('contact_requests', 'status')) {
            throw new RuntimeException('La colonne status est absente de contact_requests.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE contact_requests SET status = :status,
                processed_at = CASE
                WHEN :processed_status = "CLOSED"
                THEN CURRENT_TIMESTAMP
                ELSE processed_at
            END WHERE id = :id'
            );
        $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function tableColumns(string $table): array
    {
        $this->assertTable($table);

        if (isset($this->columnsCache[$table])) {
            return $this->columnsCache[$table];
        }

        $stmt = $this->pdo->prepare(
            'SELECT COLUMN_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name'
        );
        $stmt->execute(['table_name' => $table]);

        return $this->columnsCache[$table] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function filterData(string $table, array $data): array
    {
        $columns = $this->tableColumns($table);

        return array_filter(
            $data,
            static fn (string $column): bool => in_array($column, $columns, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function clearVehicleCover(int $vehicleId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE vehicle_images SET is_cover = 0 WHERE vehicle_id = :vehicle_id'
        );
        $stmt->execute(['vehicle_id' => $vehicleId]);
    }

    private function tableExists(string $table): bool
    {
        $this->assertTable($table);
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name'
        );
        $stmt->execute(['table_name' => $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        return in_array($column, $this->tableColumns($table), true);
    }

    private function assertTable(string $table): void
    {
        if (!in_array($table, self::ALLOWED_TABLES, true)) {
            throw new InvalidArgumentException('Table non autorisée.');
        }
    }
}
