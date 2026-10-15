<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class DashboardRepository
{
    private const ALLOWED_TABLES = [
        'vehicles',
        'vehicle_images',
        'products',
        'services',
        'contact_requests',
        'opening_hours',
    ];

    public function __construct(private PDO $pdo)
    {
    }

    public function getStatistics(): array
    {
        return [
            'vehicles' => $this->countRows(
                'vehicles',
                $this->columnExists('vehicles', 'is_published')
                    ? 'is_published = 1'
                    : null
            ),
            'products' => $this->countRows(
                'products',
                $this->columnExists('products', 'is_active')
                    ? 'is_active = 1'
                    : null
            ),
            'contacts' => $this->countPendingContacts(),
            'services' => $this->countRows(
                'services',
                $this->columnExists('services', 'is_active')
                    ? 'is_active = 1'
                    : null
            ),
        ];
    }

    public function findRecentContacts(int $limit = 5): array
    {
        if (!$this->tableExists('contact_requests')) {
            return [];
        }

        $limit = max(1, min($limit, 20));
        $columns = [
            'id',
            $this->optionalColumn('contact_requests', 'first_name'),
            $this->optionalColumn('contact_requests', 'last_name'),
            $this->optionalColumn('contact_requests', 'email'),
            $this->optionalColumn('contact_requests', 'status', "'NEW'"),
            $this->optionalColumn('contact_requests', 'created_at'),
        ];

        if ($this->columnExists('contact_requests', 'message')) {
            $columns[] = 'LEFT(message, 110) AS excerpt';
        } else {
            $columns[] = "'Demande de contact' AS excerpt";
        }

        $orderColumn = $this->columnExists('contact_requests', 'created_at')
            ? 'created_at'
            : 'id';

        $stmt = $this->pdo->query(
            'SELECT ' . implode(', ', $columns) . '
             FROM contact_requests
             ORDER BY ' . $orderColumn . ' DESC
             LIMIT ' . $limit
        );

        return $stmt->fetchAll();
    }

    public function findOpeningHours(): array
    {
        if (!$this->tableExists('opening_hours')) {
            return [];
        }

        $stmt = $this->pdo->query(
            'SELECT day_name,
                    morning_open,
                    morning_close,
                    afternoon_open,
                    afternoon_close,
                    is_closed
             FROM opening_hours
             ORDER BY day_number ASC'
        );

        return $stmt->fetchAll();
    }

    public function findCatalogPreview(): array
    {
        $items = [];
        $vehicle = $this->findLatestVehicle();
        $product = $this->findLatestProduct();
        $service = $this->findLatestService();

        if ($vehicle !== null) {
            $items[] = $vehicle;
        }

        if ($product !== null) {
            $items[] = $product;
        }

        if ($service !== null) {
            $items[] = $service;
        }

        return $items;
    }

    private function findLatestVehicle(): ?array
    {
        if (!$this->tableExists('vehicles')) {
            return null;
        }

        $imageSelect = 'NULL AS image_path';

        if ($this->tableExists('vehicle_images')) {
            $imageSelect = '(
                SELECT vi.file_path
                FROM vehicle_images vi
                WHERE vi.vehicle_id = vehicles.id
                ORDER BY vi.is_cover DESC, vi.display_order ASC, vi.id ASC
                LIMIT 1
            ) AS image_path';
        }

        $stmt = $this->pdo->query(
            'SELECT id,
                    brand,
                    model,
                    registration_year,
                    price,
                    ' . $imageSelect . '
             FROM vehicles
             ORDER BY id DESC
             LIMIT 1'
        );

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        $details = array_filter([
            $row['registration_year'] ?? null,
            !empty($row['brand']) ? $row['brand'] : null,
        ]);

        return [
            'id' => (int) $row['id'],
            'type' => 'vehicle',
            'type_label' => 'Véhicule',
            'name' => trim(($row['brand'] ?? '') . ' ' . ($row['model'] ?? '')),
            'details' => implode(' · ', $details),
            'price' => $row['price'] === null ? null : (float) $row['price'],
            'image_path' => $row['image_path'],
        ];
    }

    private function findLatestProduct(): ?array
    {
        if (!$this->tableExists('products')) {
            return null;
        }

        $priceColumn = $this->columnExists('products', 'promotional_price')
            ? 'COALESCE(promotional_price, regular_price) AS price'
            : ($this->columnExists('products', 'regular_price')
                ? 'regular_price AS price'
                : 'NULL AS price');

        $imageColumn = $this->columnExists('products', 'image_path')
            ? 'image_path'
            : 'NULL AS image_path';

        $stmt = $this->pdo->query(
            'SELECT id,
                    name,
                    ' . $priceColumn . ',
                    ' . $imageColumn . '
             FROM products
             ORDER BY id DESC
             LIMIT 1'
        );

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'type' => 'product',
            'type_label' => 'Produit',
            'name' => (string) $row['name'],
            'details' => 'Pièce & équipement',
            'price' => $row['price'] === null ? null : (float) $row['price'],
            'image_path' => $row['image_path'],
        ];
    }

    private function findLatestService(): ?array
    {
        if (!$this->tableExists('services')) {
            return null;
        }

        $priceColumn = $this->columnExists('services', 'price')
            ? 'price'
            : ($this->columnExists('services', 'base_price')
                ? 'base_price AS price'
                : 'NULL AS price');

        $stmt = $this->pdo->query(
            'SELECT id, name, ' . $priceColumn . '
             FROM services
             ORDER BY id DESC
             LIMIT 1'
        );

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'type' => 'service',
            'type_label' => 'Service',
            'name' => (string) $row['name'],
            'details' => 'Prestation RGR France',
            'price' => $row['price'] === null ? null : (float) $row['price'],
            'image_path' => null,
        ];
    }

    private function countPendingContacts(): int
    {
        if (!$this->tableExists('contact_requests')) {
            return 0;
        }

        if (!$this->columnExists('contact_requests', 'status')) {
            return $this->countRows('contact_requests');
        }

        return $this->countRows(
            'contact_requests',
            "LOWER(status) NOT IN ('processed', 'treated', 'closed', 'archived', 'traite', 'traité')"
        );
    }

    private function countRows(string $table, ?string $condition = null): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $this->assertAllowedTable($table);
        $sql = 'SELECT COUNT(*) FROM `' . $table . '`';

        if ($condition !== null) {
            $sql .= ' WHERE ' . $condition;
        }

        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    private function optionalColumn(
        string $table,
        string $column,
        string $fallback = "''"
    ): string {
        return $this->columnExists($table, $column)
            ? '`' . $column . '`'
            : $fallback . ' AS `' . $column . '`';
    }

    private function tableExists(string $table): bool
    {
        $this->assertAllowedTable($table);

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
        $this->assertAllowedTable($table);

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name'
        );
        $stmt->execute([
            'table_name' => $table,
            'column_name' => $column,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function assertAllowedTable(string $table): void
    {
        if (!in_array($table, self::ALLOWED_TABLES, true)) {
            throw new \InvalidArgumentException('Table non autorisée.');
        }
    }
}
