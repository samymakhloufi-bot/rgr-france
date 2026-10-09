<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OpeningHour;
use DateTimeImmutable;
use InvalidArgumentException;
use PDO;
use RuntimeException;

final class OpeningHourRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    private function hydrate(array $data): OpeningHour
    {
        return new OpeningHour(
            companyInformationId: (int) $data['company_information_id'],
            dayNumber: (int) $data['day_number'],
            dayName: $data['day_name'],
            morningOpen: $data['morning_open'],
            morningClose: $data['morning_close'],
            afternoonOpen: $data['afternoon_open'],
            afternoonClose: $data['afternoon_close'],
            isClosed: (bool) $data['is_closed'],
            id: (int) $data['id'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at'])
        );
    }

    private function hydrateAll(array $rows): array
    {
        return array_map(
            fn (array $row): OpeningHour => $this->hydrate($row),
            $rows
        );
    }

    public function findById(int $id): ?OpeningHour
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                'L’identifiant doit être supérieur à zéro.'
            );
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM opening_hours
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findByCompany(int $companyInformationId): array
    {
        if ($companyInformationId <= 0) {
            throw new InvalidArgumentException(
                'L’identifiant de l’entreprise doit être supérieur à zéro.'
            );
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM opening_hours
             WHERE company_information_id = :company_information_id
             ORDER BY day_number ASC'
        );

        $stmt->execute([
            'company_information_id' => $companyInformationId,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function create(OpeningHour $openingHour): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO opening_hours (
                company_information_id,
                day_number,
                day_name,
                morning_open,
                morning_close,
                afternoon_open,
                afternoon_close,
                is_closed
            ) VALUES (
                :company_information_id,
                :day_number,
                :day_name,
                :morning_open,
                :morning_close,
                :afternoon_open,
                :afternoon_close,
                :is_closed
            )'
        );

        $stmt->execute($this->toDatabaseArray($openingHour));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(OpeningHour $openingHour): void
    {
        $id = $openingHour->getId();

        if ($id === null) {
            throw new RuntimeException(
                'Impossible de modifier un horaire sans identifiant.'
            );
        }

        $stmt = $this->pdo->prepare(
            'UPDATE opening_hours
             SET company_information_id = :company_information_id,
                 day_number = :day_number,
                 day_name = :day_name,
                 morning_open = :morning_open,
                 morning_close = :morning_close,
                 afternoon_open = :afternoon_open,
                 afternoon_close = :afternoon_close,
                 is_closed = :is_closed,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        $data = $this->toDatabaseArray($openingHour);
        $data['id'] = $id;

        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                'L’identifiant doit être supérieur à zéro.'
            );
        }

        $stmt = $this->pdo->prepare(
            'DELETE FROM opening_hours WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
    }

    private function toDatabaseArray(OpeningHour $openingHour): array
    {
        return [
            'company_information_id' => $openingHour->getCompanyInformationId(),
            'day_number' => $openingHour->getDayNumber(),
            'day_name' => $openingHour->getDayName(),
            'morning_open' => $openingHour->getMorningOpen(),
            'morning_close' => $openingHour->getMorningClose(),
            'afternoon_open' => $openingHour->getAfternoonOpen(),
            'afternoon_close' => $openingHour->getAfternoonClose(),
            'is_closed' => $openingHour->isClosed() ? 1 : 0,
        ];
    }
}
