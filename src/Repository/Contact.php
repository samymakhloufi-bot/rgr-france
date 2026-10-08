<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContactRequest;
use DateTimeImmutable;
use PDO;
use RuntimeException;

final class ContactRequestRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hydrate(array $data): ContactRequest
    {
        return new ContactRequest(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            message: $data['message'],
            phone: $data['phone'],
            postalAddress: $data['postal_address'],
            postalCode: $data['postal_code'],
            city: $data['city'],
            privacyConsent: (bool) $data['privacy_consent'],
            status: $data['status'],
            assignedEmployeeId: $data['assigned_employee_id'] === null
                ? null
                : (int) $data['assigned_employee_id'],
            processedAt: $data['processed_at'] === null
                ? null
                : new DateTimeImmutable($data['processed_at']),
            id: (int) $data['id'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at'])
        );
    }

    private function hydrateAll(array $rows): array
    {
        $contactRequests = [];

        foreach ($rows as $row) {
            $contactRequests[] = $this->hydrate($row);
        }

        return $contactRequests;
    }

    private function getContactRequestParameters(ContactRequest $contactRequest): array
    {
        return [
            'assigned_employee_id' => $contactRequest->getAssignedEmployeeId(),
            'first_name' => $contactRequest->getFirstName(),
            'last_name' => $contactRequest->getLastName(),
            'email' => $contactRequest->getEmail(),
            'phone' => $contactRequest->getPhone(),
            'postal_address' => $contactRequest->getPostalAddress(),
            'postal_code' => $contactRequest->getPostalCode(),
            'city' => $contactRequest->getCity(),
            'message' => $contactRequest->getMessage(),
            'status' => $contactRequest->getStatus(),
            'privacy_consent' => $contactRequest->hasPrivacyConsent() ? 1 : 0,
            'processed_at' => $contactRequest->getProcessedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM contact_requests
             ORDER BY created_at DESC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findById(int $id): ?ContactRequest
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM contact_requests
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findByStatus(string $status): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM contact_requests
             WHERE status = :status
             ORDER BY created_at DESC'
        );

        $stmt->execute([
            'status' => $status,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findByAssignedEmployeeId(int $employeeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM contact_requests
             WHERE assigned_employee_id = :assigned_employee_id
             ORDER BY created_at DESC'
        );

        $stmt->execute([
            'assigned_employee_id' => $employeeId,
        ]);

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function findAllUnassigned(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM contact_requests
             WHERE assigned_employee_id IS NULL
             ORDER BY created_at DESC'
        );

        return $this->hydrateAll($stmt->fetchAll());
    }

    public function save(ContactRequest $contactRequest): ContactRequest
    {
        if ($contactRequest->getId() === null) {
            return $this->create($contactRequest);
        }

        return $this->update($contactRequest);
    }

    private function create(ContactRequest $contactRequest): ContactRequest
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO contact_requests (
                assigned_employee_id,
                first_name,
                last_name,
                email,
                phone,
                postal_address,
                postal_code,
                city,
                message,
                status,
                privacy_consent,
                processed_at
            ) VALUES (
                :assigned_employee_id,
                :first_name,
                :last_name,
                :email,
                :phone,
                :postal_address,
                :postal_code,
                :city,
                :message,
                :status,
                :privacy_consent,
                :processed_at
            )'
        );

        $stmt->execute($this->getContactRequestParameters($contactRequest));

        $contactRequestId = (int) $this->pdo->lastInsertId();
        $createdContactRequest = $this->findById($contactRequestId);

        if ($createdContactRequest === null) {
            throw new RuntimeException('La demande créée est introuvable.');
        }

        return $createdContactRequest;
    }

    private function update(ContactRequest $contactRequest): ContactRequest
    {
        $contactRequestId = $contactRequest->getId();

        if ($contactRequestId === null) {
            throw new RuntimeException('Impossible de modifier une demande sans identifiant.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE contact_requests SET
                assigned_employee_id = :assigned_employee_id,
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone = :phone,
                postal_address = :postal_address,
                postal_code = :postal_code,
                city = :city,
                message = :message,
                status = :status,
                privacy_consent = :privacy_consent,
                processed_at = :processed_at
             WHERE id = :id'
        );

        $parameters = $this->getContactRequestParameters($contactRequest);
        $parameters['id'] = $contactRequestId;

        $stmt->execute($parameters);

        $updatedContactRequest = $this->findById($contactRequestId);

        if ($updatedContactRequest === null) {
            throw new RuntimeException('La demande modifiée est introuvable.');
        }

        return $updatedContactRequest;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM contact_requests
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }
}
