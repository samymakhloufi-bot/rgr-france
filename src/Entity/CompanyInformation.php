<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

final class CompanyInformation
{
    private ?int $id;
    private string $companyName;
    private ?string $legalForm;
    private ?string $siren;
    private string $phone;
    private string $email;
    private string $addressLine;
    private string $postalCode;
    private string $city;
    private string $country;
    private ?string $googleMapsUrl;
    private ?string $facebookUrl;
    private ?string $instagramUrl;
    private ?string $linkedinUrl;
    private ?string $youtubeUrl;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $companyName,
        string $phone,
        string $email,
        string $addressLine,
        string $postalCode,
        string $city,
        string $country = 'France',
        ?string $legalForm = null,
        ?string $siren = null,
        ?string $googleMapsUrl = null,
        ?string $facebookUrl = null,
        ?string $instagramUrl = null,
        ?string $linkedinUrl = null,
        ?string $youtubeUrl = null,
        ?int $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;

        $this->setCompanyName($companyName);
        $this->setPhone($phone);
        $this->setEmail($email);
        $this->setAddressLine($addressLine);
        $this->setPostalCode($postalCode);
        $this->setCity($city);
        $this->setCountry($country);

        $this->legalForm = self::normalizeNullableString($legalForm);
        $this->siren = self::normalizeNullableString($siren);
        $this->googleMapsUrl = self::normalizeNullableString($googleMapsUrl);
        $this->facebookUrl = self::normalizeNullableString($facebookUrl);
        $this->instagramUrl = self::normalizeNullableString($instagramUrl);
        $this->linkedinUrl = self::normalizeNullableString($linkedinUrl);
        $this->youtubeUrl = self::normalizeNullableString($youtubeUrl);

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

    private function normalizeRequiredString(
        string $value,
        string $errorMessage
    ): string {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException($errorMessage);
        }

        return $value;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $this->normalizeRequiredString(
            $companyName,
            'Le nom de l’entreprise est obligatoire.'
        );

        $this->touch();

        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $this->normalizeRequiredString(
            $phone,
            'Le numéro de téléphone est obligatoire.'
        );

        $this->touch();

        return $this;
    }

    public function setEmail(string $email): self
    {
        $email = strtolower(trim($email));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(
                'L’adresse e-mail est invalide.'
            );
        }

        $this->email = $email;
        $this->touch();

        return $this;
    }

    public function setAddressLine(string $addressLine): self
    {
        $this->addressLine = $this->normalizeRequiredString(
            $addressLine,
            'L’adresse est obligatoire.'
        );

        $this->touch();

        return $this;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $this->normalizeRequiredString(
            $postalCode,
            'Le code postal est obligatoire.'
        );

        $this->touch();

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $this->normalizeRequiredString(
            $city,
            'La ville est obligatoire.'
        );

        $this->touch();

        return $this;
    }

    public function setCountry(string $country): self
    {
        $this->country = $this->normalizeRequiredString(
            $country,
            'Le pays est obligatoire.'
        );

        $this->touch();

        return $this;
    }

    public function setLegalForm(?string $legalForm): self
    {
        $this->legalForm = self::normalizeNullableString($legalForm);
        $this->touch();

        return $this;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = self::normalizeNullableString($siren);
        $this->touch();

        return $this;
    }

    public function setGoogleMapsUrl(?string $url): self
    {
        $this->googleMapsUrl = self::normalizeNullableString($url);
        $this->touch();

        return $this;
    }

    public function setFacebookUrl(?string $url): self
    {
        $this->facebookUrl = self::normalizeNullableString($url);
        $this->touch();

        return $this;
    }

    public function setInstagramUrl(?string $url): self
    {
        $this->instagramUrl = self::normalizeNullableString($url);
        $this->touch();

        return $this;
    }

    public function setLinkedinUrl(?string $url): self
    {
        $this->linkedinUrl = self::normalizeNullableString($url);
        $this->touch();

        return $this;
    }

    public function setYoutubeUrl(?string $url): self
    {
        $this->youtubeUrl = self::normalizeNullableString($url);
        $this->touch();

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getLegalForm(): ?string
    {
        return $this->legalForm;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getGoogleMapsUrl(): ?string
    {
        return $this->googleMapsUrl;
    }

    public function getFacebookUrl(): ?string
    {
        return $this->facebookUrl;
    }

    public function getInstagramUrl(): ?string
    {
        return $this->instagramUrl;
    }

    public function getLinkedinUrl(): ?string
    {
        return $this->linkedinUrl;
    }

    public function getYoutubeUrl(): ?string
    {
        return $this->youtubeUrl;
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