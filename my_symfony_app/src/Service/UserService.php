<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\ListObject;
use App\Service\Data\UserParams;
use App\Service\Logic\UserAvatar;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserService
{
    //Переменные, константы и конструктор класса
    private const REGISTER_METHOD = 1;

    private const UPDATE_METHOD = 2;

    private const OK_STATUS = 'ok';

    private UserRepository $userRepository;

    private UserAvatar $avatar;

    public function __construct(UserRepository $userRepository, UserAvatar $avatar)
    {
        $this->userRepository = $userRepository;
        $this->avatar = $avatar;
    }

    //Публичные методы
    public function getUser(int $userId): User
    {
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new \Exception('User with ID ' . $userId . ' does\'t exist ');
        }

        return $user;
    }

    public function getAllUsers(): ?array
    {
        $users = $this->userRepository->findAll();
        $list = [];
        foreach ($users as $user) {
            $list[] = new ListObject(
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $user->getAvatarPath()
            );
        }
        return $list;
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        $this->avatar->delete($user->getAvatarPath());
        $this->userRepository->delete($user);
    }

    public function createUser(UserParams $userParams, ?UploadedFile $avatar, ?string $destination): int
    {
        if ($this->avatar->validateExtention($avatar) === false) {
            throw new \Exception("Invalid Image extention. Must be .png .gif .jpeg .jpg");
        }

        if ($field = $this->checkUniqueFields($userParams, null, self::REGISTER_METHOD)) {
            throw new \Exception('Your ' . $field . ' has been already taken');
        }

        $user = $this->createFromParams($userParams);

        $userId = $this->userRepository->store($user);

        $avatarPath = $this->avatar->save($avatar, $userId, $destination);
        $user->setAvatarPath($avatarPath);

        $this->userRepository->store($user);

        return $userId;
    }

    public function editUser(UserParams $userParams, ?UploadedFile $avatar, ?string $destination): int
    {
        if ($this->avatar->validateExtention($avatar) === false) {
            throw new \Exception("Invalid Image extention. Must be .png .gif .jpeg .jpg");
        }

        $userId = $userParams->getId();
        $user = $this->userRepository->findById($userId);

        if ($field = $this->checkUniqueFields($userParams, $user, self::UPDATE_METHOD)) {
            throw new \Exception('Your ' . $field . ' has been already taken');
        }

        $this->updateFromParams($userParams, $user);

        if ($avatar) {
            $prev = $this->userRepository->findById($userParams->getId())->getAvatarPath();
            $this->avatar->delete($prev);
            $new = $this->avatar->save($avatar, $userId, $destination);
            $user->setAvatarPath($new);
        }

        $this->userRepository->store($user);

        return $userId;
    }

    //Приватные методы
    private function createFromParams(UserParams $params): User
    {
        return new User(
            null,
            $params->getFirstName(),
            $params->getLastName(),
            $params->getMiddleName(),
            $params->getGender(),
            $params->getBirthDate(),
            $params->getEmail(),
            $params->getPhone(),
            null,
        );
    }

    private function updateFromParams(UserParams $params, User $user): void
    {
        $user->setFirstName($params->getFirstName());
        $user->setLastName($params->getLastName());
        $user->setMiddleName($params->getMiddleName());
        $user->setGender($params->getGender());
        $user->setBirthDate($params->getBirthDate());
        $user->setEmail($params->getEmail());
        $user->setPhone($params->getPhone());
    }

    private function checkUniqueFields(UserParams $userParams, ?User $user, int $method): ?string
    {
        if ($method === self::UPDATE_METHOD) {
            if (!$this->isEmailUnique($user->getEmail(), $userParams->getEmail())) {
                return 'email';
            }
            if (!$this->isPhoneUnique($user->getPhone(), $userParams->getPhone())) {
                return 'phone';
            }
        }
        if ($method === self::REGISTER_METHOD) {
            if ($this->userRepository->findByEmail($userParams->getEmail())) {
                return 'email';
            }
            if ($this->userRepository->findByPhone($userParams->getPhone())) {
                return 'phone';
            }
        }
        return null;
    }

    private function isEmailUnique(string $email, ?string $newEmail): bool
    {
        return ($email !== $newEmail && $this->userRepository->findByEmail($newEmail)) ? false : true;
    }

    private function isPhoneUnique(?string $phone, ?string $newPhone): bool
    {
        return ($phone !== $newPhone && $this->userRepository->findByPhone($newPhone)) ? false : true;
    }
}