<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

final class ContactRequest
{
    public const STATUS_NEW = 'NEW';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_CLOSED = 'CLOSED';
    public const STATUS_ARCHIVED = 'ARCHIVED';

    private ?int $id;
    private ?int $assignedEmployeeId;
    private string $firstName;
    private string $lastName;
    private string $email;
    private ?string $phone;
    private ?string $postalAddress;
    private ?string $postalCode;
    private ?string $city;
    private string $message;
    private string $status;
    private bool $privacyConsent;
    private ?DateTimeImmutable $processedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $message,
        ?string $phone = null,
        ?string $postalAddress = null,
        ?string $postalCode = null,
        ?string $city = null,
        bool $privacyConsent = false,
        string $status = self::STATUS_NEW,
        ?int $assignedEmployeeId = null,
        ?DateTimeImmutable $processedAt = null,
        ?int $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->setAssignedEmployeeId($assignedEmployeeId);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->phone = self::normalizeNullableString($phone);
        $this->postalAddress = self::normalizeNullableString($postalAddress);
        $this->postalCode = self::normalizeNullableString($postalCode);
        $this->city = self::normalizeNullableString($city);
        $this->setMessage($message);
        $this->setStatus($status);
        $this->privacyConsent = $privacyConsent;
        $this->processedAt = $processedAt;
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

    private static function normalizeRequiredString(string $value, string $message): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException($message);
        }

        return $value;
    }

    public function setAssignedEmployeeId(?int $assignedEmployeeId): self
    {
        if ($assignedEmployeeId !== null && $assignedEmployeeId <= 0) {
            throw new InvalidArgumentException('L’identifiant de l’employé est invalide.');
        }

        $this->assignedEmployeeId = $assignedEmployeeId;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = self::normalizeRequiredString(
            $firstName,
            'Le prénom est obligatoire.'
        );

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = self::normalizeRequiredString(
            $lastName,
            'Le nom est obligatoire.'
        );

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setEmail(string $email): self
    {
        $email = strtolower(trim($email));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('L’adresse e-mail est invalide.');
        }

        $this->email = $email;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = self::normalizeNullableString($phone);
        $this->touch();

        return $this;
    }

    public function setPostalAddress(?string $postalAddress): self
    {
        $this->postalAddress = self::normalizeNullableString($postalAddress);
        $this->touch();

        return $this;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = self::normalizeNullableString($postalCode);
        $this->touch();

        return $this;
    }

    public function setCity(?string $city): self
    {
        $this->city = self::normalizeNullableString($city);
        $this->touch();

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = self::normalizeRequiredString(
            $message,
            'Le message est obligatoire.'
        );

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setStatus(string $status): self
    {
        $allowedStatuses = [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_CLOSED,
            self::STATUS_ARCHIVED,
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException('Le statut de la demande est invalide.');
        }

        $this->status = $status;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function getId(): ?int                            {return $this->id;}

    public function getAssignedEmployeeId(): ?int            {return $this->assignedEmployeeId;}

    public function getFirstName(): string                   {return $this->firstName;}

    public function getLastName(): string                    {return $this->lastName;}

    public function getEmail(): string                       {return $this->email;}

    public function getPhone(): ?string                      {return $this->phone;}

    public function getPostalAddress(): ?string              {return $this->postalAddress;}

    public function getPostalCode(): ?string                 {return $this->postalCode;}

    public function getCity(): ?string                       {return $this->city;}

    public function getMessage(): string                     {return $this->message;}

    public function getStatus(): string                      {return $this->status;}

    public function hasPrivacyConsent(): bool                {return $this->privacyConsent;}

    public function grantPrivacyConsent(): self              {$this->privacyConsent = true; $this->touch(); return $this;}

    public function revokePrivacyConsent(): self             {$this->privacyConsent = false; $this->touch(); return $this;}

    public function markAsInProgress(): self                 {$this->status = self::STATUS_IN_PROGRESS; $this->touch(); return $this;}

    public function close(?DateTimeImmutable $date = null): self
    {
        $this->status = self::STATUS_CLOSED;
        $this->processedAt = $date ?? new DateTimeImmutable();
        $this->touch();

        return $this;
    }

    public function archive(): self                          {$this->status = self::STATUS_ARCHIVED; $this->touch(); return $this;}

    public function getProcessedAt(): ?DateTimeImmutable     {return $this->processedAt;}

    public function getCreatedAt(): DateTimeImmutable        {return $this->createdAt;}

    public function getUpdatedAt(): DateTimeImmutable        {return $this->updatedAt;}

    private function touch(): void                           {$this->updatedAt = new DateTimeImmutable();}
}
