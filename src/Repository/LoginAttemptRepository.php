<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class LoginAttemptRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function countRecentAttempts(
        string $identifierHash,
        int $minutes = 15
    ): int {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM login_attempts
             WHERE identifier_hash = :identifier_hash
               AND attempted_at >= DATE_SUB(
                   NOW(),
                   INTERVAL :minutes MINUTE
               )'
        );

        $stmt->bindValue(
            'identifier_hash',
            $identifierHash
        );

        $stmt->bindValue(
            'minutes',
            $minutes,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function recordFailure(string $identifierHash): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO login_attempts (
                identifier_hash,
                attempted_at
            ) VALUES (
                :identifier_hash,
                NOW()
            )'
        );

        $stmt->execute([
            'identifier_hash' => $identifierHash,
        ]);
    }

    public function clearAttempts(string $identifierHash): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM login_attempts
             WHERE identifier_hash = :identifier_hash'
        );

        $stmt->execute([
            'identifier_hash' => $identifierHash,
        ]);
    }

    public function deleteExpiredAttempts(int $hours = 24): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM login_attempts
             WHERE attempted_at < DATE_SUB(
                 NOW(),
                 INTERVAL :hours HOUR
             )'
        );

        $stmt->bindValue(
            'hours',
            $hours,
            PDO::PARAM_INT
        );

        $stmt->execute();
    }
}