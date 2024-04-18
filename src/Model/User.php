<?php
declare(strict_types=1);

namespace App\Model;

class User
{
    public function __construct(
        private ?int $userId,
        private string $firstName,
        private string $lastName,
        private ?string $middleName,
        private string $gender,
        private string $birthDate,
        private string $email,
        private ?string $phone,
        private ?string $avatarPath
    ) {

    }

    public function getId(): ?int
    {
        return $this->userId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getGender(): string
    {
        return $this->gender;
    }
    public function getBirthDate(): string
    {
        return $this->birthDate;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }
}