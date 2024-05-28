<?php
declare(strict_types=1);

//Переделать логику: если аватар не загружен, то ничего не сохраняем
//На фронте если аватар есть - отображать его
//Если аватара нет, отображать стандартный

namespace App\Service\Logic;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAvatar
{
    //Переменные, константы и конструктор класса
    private const ALLOWED_EXTENTIONS = ['png', 'gif', 'jpg', 'jpeg'];

    public function __construct()
    {
    }

    //Публичные методы
    public function save(?UploadedFile $avatar, int $userId, string $destination): ?string
    {
        $ext = $this->validateExtention($avatar);

        if ($avatar && $ext) {
            $fileName = 'avatar' . $userId . '.' . $ext;
            $avatar->move($destination, $fileName);
            return $fileName;
        }
        return null;
    }

    public function delete(?string $avatarName): void
    {
        try {
            unlink('uploads/' . $avatarName);
        } catch (\ErrorException $e) {
            return;
        }
    }

    public function isExtentionAllowed(?string $ext): bool
    {
        return in_array($ext, self::ALLOWED_EXTENTIONS);
    }

    public function validateExtention(?UploadedFile $avatar): mixed
    {
        if ($avatar) {
            $ext = $avatar->getClientOriginalExtension();
            return $this->isExtentionAllowed($ext) ? $ext : false;
        } else {
            return null;
        }
    }
}