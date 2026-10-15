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

    /*
     * Transforme une ligne SQL en objet User.
     */
    private function hydrate(array $data): User
    {
        return new User(
            username: (string) $data['username'],
            passwordHash: (string) $data['password_hash'],
            role: (string) $data['role'],
            firstName: $data['first_name'] !== null
                ? (string) $data['first_name']
                : null,
            lastName: $data['last_name'] !== null
                ? (string) $data['last_name']
                : null,
            email: $data['email'] !== null
                ? (string) $data['email']
                : null,
            isActive: (bool) $data['is_active'],
            id: (int) $data['id'],
            lastLoginAt: !empty($data['last_login_at'])
                ? new DateTimeImmutable($data['last_login_at'])
                : null,
            createdAt: new DateTimeImmutable(
                $data['created_at']
            ),
            updatedAt: new DateTimeImmutable(
                $data['updated_at']
            )
        );
    }

    /*
     * Transforme plusieurs lignes SQL.
     */
    private function hydrateAll(array $rows): array
    {
        $users = [];

        foreach ($rows as $row) {
            $users[] = $this->hydrate($row);
        }

        return $users;
    }

    /*
     * Prépare les données utilisées pour INSERT et UPDATE.
     */
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
            'last_login_at' => $user
                ->getLastLoginAt()
                ?->format('Y-m-d H:i:s'),
        ];
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM users
             ORDER BY last_name ASC,
                      first_name ASC,
                      username ASC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllActive(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM users
             WHERE is_active = :is_active
             ORDER BY last_name ASC,
                      first_name ASC,
                      username ASC'
        );

        $stmt->execute([
            'is_active' => 1,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllEmployees(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM users
             WHERE role = :role
             ORDER BY last_name ASC,
                      first_name ASC,
                      username ASC'
        );

        $stmt->execute([
            'role' => User::ROLE_EMPLOYEE,
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

        return $data === false
            ? null
            : $this->hydrate($data);
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

        return $data === false
            ? null
            : $this->hydrate($data);
    }

    public function findActiveByUsername(
        string $username
    ): ?User {
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

        return $data === false
            ? null
            : $this->hydrate($data);
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

        return $data === false
            ? null
            : $this->hydrate($data);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM users
             WHERE email = :email'
        );

        $stmt->execute([
            'email' => strtolower(trim($email)),
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /*
     * Crée ou modifie selon la présence de l’ID.
     */
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

        $stmt->execute(
            $this->getUserParameters($user)
        );

        $userId = (int) $this->pdo->lastInsertId();
        $createdUser = $this->findById($userId);

        if ($createdUser === null) {
            throw new RuntimeException(
                'L’utilisateur créé est introuvable.'
            );
        }

        return $createdUser;
    }

    private function update(User $user): User
    {
        $userId = $user->getId();

        if ($userId === null) {
            throw new RuntimeException(
                'Impossible de modifier un utilisateur sans identifiant.'
            );
        }

        $parameters = $this->getUserParameters($user);
        $parameters['id'] = $userId;

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

        $stmt->execute($parameters);

        $updatedUser = $this->findById($userId);

        if ($updatedUser === null) {
            throw new RuntimeException(
                'L’utilisateur modifié est introuvable.'
            );
        }

        return $updatedUser;
    }

    public function updatePasswordHash(
        int $id,
        string $passwordHash
    ): void {
        $passwordHash = trim($passwordHash);

        if ($passwordHash === '') {
            throw new RuntimeException(
                'Le hash du mot de passe est obligatoire.'
            );
        }

        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET password_hash = :password_hash
             WHERE id = :id'
        );

        $stmt->execute([
            'password_hash' => $passwordHash,
            'id' => $id,
        ]);
    }

    public function recordLogin(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET last_login_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);
    }

    public function setActive(
        int $id,
        bool $isActive
    ): void {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET is_active = :is_active
             WHERE id = :id'
        );

        $stmt->execute([
            'is_active' => $isActive ? 1 : 0,
            'id' => $id,
        ]);
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