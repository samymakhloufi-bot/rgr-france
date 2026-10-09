<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use DateTimeImmutable;
use PDO;
use RuntimeException;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $data): User
    {
        return new User(
            username: $data['username'],
            passwordHash: $data['password_hash'],
            role: $data['role'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            isActive: (bool) $data['is_active'],
            id: (int) $data['id'],
            lastLoginAt: $data['last_login_at'] === null
                ? null
                : new DateTimeImmutable($data['last_login_at']),
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at'])
        );
    }

    private function hydrateAll(array $rows): array
    {
        $users = [];

        foreach ($rows as $row) {
            $users[] = $this->hydrate($row);
        }

        return $users;
    }

    private function getUserParameters(User $user): array
    {
        return [
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'password_hash' => $user->getPasswordHash(),
            'role' => $user->getRole(),
            'is_active' => $user->isActive() ? 1 : 0,
            'last_login_at' => $user->getLastLoginAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM users
             ORDER BY last_name ASC, first_name ASC, username ASC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM users
             WHERE is_active = :is_active
             ORDER BY last_name ASC, first_name ASC, username ASC'
        );

        $stmt->execute([
            'is_active' => 1,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM users
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM users
             WHERE username = :username
             LIMIT 1'
        );

        $stmt->execute([
            'username' => trim($username),
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findActiveByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM users
             WHERE username = :username
               AND is_active = :is_active
             LIMIT 1'
        );

        $stmt->execute([
            'username' => trim($username),
            'is_active' => 1,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $stmt->execute([
            'email' => strtolower(trim($email)),
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function save(User $user): User
    {
        if ($user->getId() === null) {
            return $this->create($user);
        }

        return $this->update($user);
    }

    private function create(User $user): User
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (
                first_name,
                last_name,
                email,
                username,
                password_hash,
                role,
                is_active,
                last_login_at
            ) VALUES (
                :first_name,
                :last_name,
                :email,
                :username,
                :password_hash,
                :role,
                :is_active,
                :last_login_at
            )'
        );

        $stmt->execute($this->getUserParameters($user));

        $userId = (int) $this->pdo->lastInsertId();
        $createdUser = $this->findById($userId);

        if ($createdUser === null) {
            throw new RuntimeException('L’utilisateur créé est introuvable.');
        }

        return $createdUser;
    }

    private function update(User $user): User
    {
        $userId = $user->getId();

        if ($userId === null) {
            throw new RuntimeException('Impossible de modifier un utilisateur sans identifiant.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE users SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                username = :username,
                password_hash = :password_hash,
                role = :role,
                is_active = :is_active,
                last_login_at = :last_login_at
             WHERE id = :id'
        );

        $parameters = $this->getUserParameters($user);
        $parameters['id'] = $userId;

        $stmt->execute($parameters);

        $updatedUser = $this->findById($userId);

        if ($updatedUser === null) {
            throw new RuntimeException('L’utilisateur modifié est introuvable.');
        }

        return $updatedUser;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM users
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }
}
