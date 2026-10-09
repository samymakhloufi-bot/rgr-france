<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

final class OpeningHour
{
    private ?int $id;
    private int $companyInformationId;
    private int $dayNumber;
    private string $dayName;
    private ?string $morningOpen;
    private ?string $morningClose;
    private ?string $afternoonOpen;
    private ?string $afternoonClose;
    private bool $isClosed;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $companyInformationId,
        int $dayNumber,
        string $dayName,
        ?string $morningOpen = null,
        ?string $morningClose = null,
        ?string $afternoonOpen = null,
        ?string $afternoonClose = null,
        bool $isClosed = false,
        ?int $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->setCompanyInformationId($companyInformationId);
        $this->setDayNumber($dayNumber);
        $this->setDayName($dayName);
        $this->isClosed = $isClosed;

        if ($isClosed) {
            $this->morningOpen = null;
            $this->morningClose = null;
            $this->afternoonOpen = null;
            $this->afternoonClose = null;
        } else {
            $this->setSchedule(
                $morningOpen,
                $morningClose,
                $afternoonOpen,
                $afternoonClose
            );
        }

        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    private static function normalizeTime(?string $time): ?string
    {
        if ($time === null || trim($time) === '') {
            return null;
        }

        $time = trim($time);

        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $time)) {
            throw new InvalidArgumentException(
                'L’heure doit respecter le format HH:MM ou HH:MM:SS.'
            );
        }

        return strlen($time) === 5 ? $time . ':00' : $time;
    }

    private static function validateTimeRange(
        ?string $openingTime,
        ?string $closingTime,
        string $periodName
    ): void {
        if (($openingTime === null) !== ($closingTime === null)) {
            throw new InvalidArgumentException(
                "Les horaires de {$periodName} doivent contenir une ouverture et une fermeture."
            );
        }

        if (
            $openingTime !== null
            && $closingTime !== null
            && $openingTime >= $closingTime
        ) {
            throw new InvalidArgumentException(
                "L’heure de fermeture de {$periodName} doit être postérieure à l’ouverture."
            );
        }
    }

    public function setCompanyInformationId(int $companyInformationId): self
    {
        if ($companyInformationId <= 0) {
            throw new InvalidArgumentException(
                'L’identifiant de l’entreprise doit être supérieur à zéro.'
            );
        }

        $this->companyInformationId = $companyInformationId;
        $this->touch();

        return $this;
    }

    public function setDayNumber(int $dayNumber): self
    {
        if ($dayNumber < 1 || $dayNumber > 7) {
            throw new InvalidArgumentException(
                'Le numéro du jour doit être compris entre 1 et 7.'
            );
        }

        $this->dayNumber = $dayNumber;
        $this->touch();

        return $this;
    }

    public function setDayName(string $dayName): self
    {
        $dayName = trim($dayName);

        if ($dayName === '') {
            throw new InvalidArgumentException(
                'Le nom du jour est obligatoire.'
            );
        }

        $this->dayName = $dayName;
        $this->touch();

        return $this;
    }

    public function setSchedule(
        ?string $morningOpen,
        ?string $morningClose,
        ?string $afternoonOpen,
        ?string $afternoonClose
    ): self {
        $morningOpen = self::normalizeTime($morningOpen);
        $morningClose = self::normalizeTime($morningClose);
        $afternoonOpen = self::normalizeTime($afternoonOpen);
        $afternoonClose = self::normalizeTime($afternoonClose);

        self::validateTimeRange($morningOpen, $morningClose, 'la matinée');
        self::validateTimeRange($afternoonOpen, $afternoonClose, 'l’après-midi');

        if ($morningOpen === null && $afternoonOpen === null) {
            throw new InvalidArgumentException(
                'Une journée ouverte doit contenir au moins une plage horaire.'
            );
        }

        $this->morningOpen = $morningOpen;
        $this->morningClose = $morningClose;
        $this->afternoonOpen = $afternoonOpen;
        $this->afternoonClose = $afternoonClose;
        $this->isClosed = false;
        $this->touch();

        return $this;
    }

    public function close(): self
    {
        $this->isClosed = true;
        $this->morningOpen = null;
        $this->morningClose = null;
        $this->afternoonOpen = null;
        $this->afternoonClose = null;
        $this->touch();

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyInformationId(): int
    {
        return $this->companyInformationId;
    }

    public function getDayNumber(): int
    {
        return $this->dayNumber;
    }

    public function getDayName(): string
    {
        return $this->dayName;
    }

    public function getMorningOpen(): ?string
    {
        return $this->morningOpen;
    }

    public function getMorningClose(): ?string
    {
        return $this->morningClose;
    }

    public function getAfternoonOpen(): ?string
    {
        return $this->afternoonOpen;
    }

    public function getAfternoonClose(): ?string
    {
        return $this->afternoonClose;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        if (isset($this->updatedAt)) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }
}

