<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\UserTable;
use App\Model\User;
use App\Database\ConnectionProvider;

class UserController
{
    //Переменные и конструктор класса
    private const BASE_IMAGE_PATH = 'C:\webcourse\php\uploads\base-image.png';
    private UserTable $table;

    //Публичные методы для вызова из других файлов
    public function __construct()
    {
        $connection = ConnectionProvider::getConnection();
        $this->table = new UserTable($connection);
    }

    public function index(): void
    {
        require_once __DIR__ . '/../../src/View/hub.php';
    }

    public function createUser(array $request, array $avatarPath): void
    {
        try {
            $user = new User(
                null,

                $request['first_name'] ? $request['first_name']
                : throw new \Exception("MISSING DATA: first_name."),

                $request['last_name'] ? $request['last_name']
                : throw new \Exception("MISSING DATA: last_name."),
                ($request['middle_name'] === null) ? $request['middle_name'] : null,

                $request['gender'] ? $request['gender']
                : throw new \Exception("MISSING DATA: gender."),

                $request['birth_date'] ? $request['birth_date']
                : throw new \Exception("MISSING DATA: birth_date."),

                $request['email'] ? $request['email']
                : throw new \Exception("MISSING DATA: email."),

                ($request['phone'] != null) ? $request['phone'] : null,

                null,
            );
            $userId = $this->table->saveUserToDatabase($user);
            $type = $this->defineFileExtention($avatarPath);
            $this->saveAvatar($avatarPath, $userId, $type);
        } catch (\Exception $e) {
            echo $e . "<br>";
            die();
        }
        $redirectUrl = "/php/show_user?userId=$userId";
        header('Location: ' . $redirectUrl, true, 303);
    }

    public function viewUser(array $request): void
    {
        $userId = $this->defineUserId($request);
        try {
            $user = $this->table->find($userId);
            if ($user === null) {
                die('ERROR. This user doesn\'t exist');
            }
        } catch (\Exception $e) {
            echo $e;
            die();
        }
        require __DIR__ . '/../View/view_form.php';
    }

    public function viewAllUsers(): void
    {
        try {
            $data = $this->table->findAll();
        } catch (\Exception $e) {
            echo $e;
            die();
        }
        require_once __DIR__ . '/../View/user_list.php';
    }

    public function viewUpdateForm(array $request): void
    {
        $userId = $this->defineUserId($request);
        try {
            $user = $this->table->find($userId);
            if ($user === null) {
                die('ERROR. This user doesn\'t exist');
            }
        } catch (\Exception $e) {
            echo $e;
            die();
        }
        require_once __DIR__ . '/../View/update_form.php';
    }

    public function updateUser(array $getRequest, array $postRequest, array $avatarPath): void
    {
        $userId = $this->defineUserId($getRequest);
        try {
            $user = $this->table->updateData($postRequest, $userId);
            if ($avatarPath['tmp_name'] != null) {
                $type = $this->defineFileExtention($avatarPath);
                if ($type != null) {
                    $this->table->deleteAvatar($userId);
                    $this->saveAvatar($avatarPath, $userId, $type);
                }
            }
            if ($user === null) {
                die('ERROR. This user doesn\'t exist');
            }
        } catch (\Exception $e) {
            echo $e;
            die();
        }
        $redirectUrl = "/php/show_user?userId=$userId";
        header('Location: ' . $redirectUrl, true, 303);
    }

    public function deleteUser(array $request): void
    {
        $userId = $this->defineUserId($request);
        try {
            $this->table->delete($userId);
        } catch (\Exception $e) {
            echo $e;
            die();
        }
        header('Location: ./view_all_users', true, 303);
    }

    //Внутренние методы для обработки данных и внутренней логики
    private function defineUserId(array $request): int
    {
        $userId = $request['userId'] ?? null;
        try {
            if ($userId === null) {
                throw new \InvalidArgumentException('Parameter id is not defined');
            }
        } catch (\InvalidArgumentException $e) {
            echo $e;
            die();
        }
        return (int) $userId;
    }

    private function saveAvatar(array $file, int $userId, ?string $type): void
    {
        $tmpFile = $file['tmp_name'];
        $newFileName = $this->setAvatarName($type, $userId);
        $newFilePath = 'uploads/' . $newFileName;
        $this->table->updateAvatar($newFileName, $userId);
        ($tmpFile === '')
            ? copy(UserController::BASE_IMAGE_PATH, $newFilePath)
            : move_uploaded_file($tmpFile, $newFilePath);
    }

    private function setAvatarName(?string $type, int $userId): string
    {
        if ($type === null) {
            $type = 'image/png';
        }
        $type = str_replace('image/', '.', $type);
        $newFileName = 'avatar' . $userId . $type;
        return $newFileName;
    }

    private function defineFileExtention(?array $file): ?string
    {
        (empty($file['tmp_name']))
            ? $type = null
            : $type = mime_content_type($file['tmp_name']);
        try {
            switch ($type) {
                case 'image/png':
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/gif':
                case null;
                    break;
                default:
                    throw new \Exception("WRONG Image type. Must be .png, .jpeg, .jpg or .gif");
            }
        } catch (\Exception $e) {
            echo $e;
            die();
        }
        return $type;
    }
}