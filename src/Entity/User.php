<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

final class User
{
    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_EMPLOYEE = 'EMPLOYEE';

    private ?int $id;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $email;
    private string $username;
    private string $passwordHash;
    private string $role;
    private bool $isActive;
    private ?DateTimeImmutable $lastLoginAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $username,
        string $passwordHash,
        string $role = self::ROLE_EMPLOYEE,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        bool $isActive = true,
        ?int $id = null,
        ?DateTimeImmutable $lastLoginAt = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->setUsername($username);
        $this->setPasswordHash($passwordHash);
        $this->setRole($role);
        $this->firstName = self::normalizeNullableString($firstName);
        $this->lastName = self::normalizeNullableString($lastName);
        $this->setEmail($email);
        $this->isActive = $isActive;
        $this->lastLoginAt = $lastLoginAt;
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

    public function setRole(string $role): self
    {
        $allowedRoles = [self::ROLE_ADMIN, self::ROLE_EMPLOYEE];

        if (!in_array($role, $allowedRoles, true)) {
            throw new InvalidArgumentException('Le rôle utilisateur est invalide.');
        }
        $this->role = $role;
        if (isset($this->updatedAt)) {
            $this->touch();
        }
        return $this;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = self::normalizeNullableString($firstName);
        $this->touch();

        return $this;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = self::normalizeNullableString($lastName);
        $this->touch();

        return $this;
    }

    public function setEmail(?string $email): self
    {
        $email = self::normalizeNullableString($email);

        if ($email !== null && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('L’adresse e-mail est invalide.');
        }

        $this->email = $email === null ? null : strtolower($email);

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $username = trim($username);

        if ($username === '') {
            throw new InvalidArgumentException('L’identifiant de connexion est obligatoire.');
        }

        $this->username = $username;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $passwordHash = trim($passwordHash);

        if ($passwordHash === '') {
            throw new InvalidArgumentException('Le hash du mot de passe est obligatoire.');
        }

        $this->passwordHash = $passwordHash;

        if (isset($this->updatedAt)) {
            $this->touch();
        }

        return $this;
    }

    public function getId(): ?int    {return $this->id;}

    public function getFirstName(): ?string     {return $this->firstName;}

    public function getLastName(): ?string      {return $this->lastName;}

    public function getEmail(): ?string         {return $this->email;}

    public function getUsername(): string   {return $this->username;}

    public function getPasswordHash(): string    {return $this->passwordHash;}

    public function getRole(): string    {return $this->role;}

    public function isAdmin(): bool         {return $this->role === self::ROLE_ADMIN;}

    public function isEmployee(): bool      {return $this->role === self::ROLE_EMPLOYEE;}

    public function isActive(): bool        {return $this->isActive;}

    public function activate(): self        {$this->isActive = true; $this->touch(); return $this;}

    public function deactivate(): self      {$this->isActive = false; $this->touch(); return $this;}

    public function getLastLoginAt(): ?DateTimeImmutable    {return $this->lastLoginAt;}

    public function recordLogin(?DateTimeImmutable $date = null): self  {$this->lastLoginAt = $date ?? new DateTimeImmutable();$this->touch(); return $this;}

    public function getCreatedAt(): DateTimeImmutable       {return $this->createdAt;}

    public function getUpdatedAt(): DateTimeImmutable       {return $this->updatedAt;}

    private function touch(): void           {$this->updatedAt = new DateTimeImmutable();}

}
