<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CompanyInformation;
use DateTimeImmutable;
use InvalidArgumentException;
use PDO;
use RuntimeException;

final class CompanyInformationRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    private function hydrate(array $data): CompanyInformation
    {
        return new CompanyInformation(
            companyName: $data['company_name'],
            phone: $data['phone'],
            email: $data['email'],
            addressLine: $data['address_line'],
            postalCode: $data['postal_code'],
            city: $data['city'],
            country: $data['country'],
            legalForm: $data['legal_form'],
            siren: $data['siren'],
            googleMapsUrl: $data['google_maps_url'],
            facebookUrl: $data['facebook_url'],
            instagramUrl: $data['instagram_url'],
            linkedinUrl: $data['linkedin_url'],
            youtubeUrl: $data['youtube_url'],
            id: (int) $data['id'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at'])
        );
    }

    public function findMain(): ?CompanyInformation
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM company_information
             ORDER BY id ASC
             LIMIT 1'
        );

        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function findById(int $id): ?CompanyInformation
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                'L’identifiant doit être supérieur à zéro.'
            );
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM company_information
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        return $data === false ? null : $this->hydrate($data);
    }

    public function create(CompanyInformation $company): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO company_information (
                company_name,
                legal_form,
                siren,
                phone,
                email,
                address_line,
                postal_code,
                city,
                country,
                google_maps_url,
                facebook_url,
                instagram_url,
                linkedin_url,
                youtube_url
            ) VALUES (
                :company_name,
                :legal_form,
                :siren,
                :phone,
                :email,
                :address_line,
                :postal_code,
                :city,
                :country,
                :google_maps_url,
                :facebook_url,
                :instagram_url,
                :linkedin_url,
                :youtube_url
            )'
        );

        $stmt->execute($this->toDatabaseArray($company));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(CompanyInformation $company): void
    {
        $id = $company->getId();

        if ($id === null) {
            throw new RuntimeException(
                'Impossible de modifier une entreprise sans identifiant.'
            );
        }

        $stmt = $this->pdo->prepare(
            'UPDATE company_information
             SET company_name = :company_name,
                 legal_form = :legal_form,
                 siren = :siren,
                 phone = :phone,
                 email = :email,
                 address_line = :address_line,
                 postal_code = :postal_code,
                 city = :city,
                 country = :country,
                 google_maps_url = :google_maps_url,
                 facebook_url = :facebook_url,
                 instagram_url = :instagram_url,
                 linkedin_url = :linkedin_url,
                 youtube_url = :youtube_url,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        $data = $this->toDatabaseArray($company);
        $data['id'] = $id;

        $stmt->execute($data);
    }

    private function toDatabaseArray(CompanyInformation $company): array
    {
        return [
            'company_name' => $company->getCompanyName(),
            'legal_form' => $company->getLegalForm(),
            'siren' => $company->getSiren(),
            'phone' => $company->getPhone(),
            'email' => $company->getEmail(),
            'address_line' => $company->getAddressLine(),
            'postal_code' => $company->getPostalCode(),
            'city' => $company->getCity(),
            'country' => $company->getCountry(),
            'google_maps_url' => $company->getGoogleMapsUrl(),
            'facebook_url' => $company->getFacebookUrl(),
            'instagram_url' => $company->getInstagramUrl(),
            'linkedin_url' => $company->getLinkedinUrl(),
            'youtube_url' => $company->getYoutubeUrl(),
        ];
    }
}

