<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\UserTable;
use App\Model\User;
use App\Database\ConnectionProvider;


class UserController
{
    private const BASE_IMAGE_PATH = 'C:\webcourse\php\uploads\base-image.png';
    private UserTable $table;

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

    private function saveAvatar(array $file, int $userId): void
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
        $tmpFile = $file['tmp_name'];
        $newFileName = $this->editAvatarName($type, $userId);
        $newFilePath = 'uploads/' . $newFileName;
        $this->table->updateAvatar($newFileName, $userId);
        ($tmpFile === '')
            ? copy(UserController::BASE_IMAGE_PATH, $newFilePath)
            : move_uploaded_file($tmpFile, $newFilePath);
    }

    private function editAvatarName(?string $type, int $userId): string
    {
        if ($type === null) {
            $type = 'image/png';
        }
        $type = str_replace('image/', '.', $type);
        $newFileName = 'avatar' . $userId . $type;
        return $newFileName;
    }

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
            $this->saveAvatar($avatarPath, $userId);
        } catch (\Exception $e) {
            echo $e . "<br>";
            die();
        }
        $redirectUrl = "/php/show_user?userId=$userId";
        header('Location: ' . $redirectUrl, true, 303);
    }
    public function viewUser(array $request): void
    {
        $userId = $this->defineUserId($_GET);
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

    public function showUpdateForm(array $request): void
    {
        $userId = $this->defineUserId($_GET);
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

    public function updateUser(array $request, array $avatarPath): void
    {
        $userId = $this->defineUserId($_GET);
        try {
            $user = $this->table->updateData($_POST, $userId);
            if ($avatarPath['tmp_name'] != null) {
                $this->saveAvatar($avatarPath, $userId);
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
        $userId = $this->defineUserId($_GET);
        try {
            $this->table->delete($userId);
        } catch (\Exception $e) {
            echo $e;
            die();
        }
        header('Location: ./view_all_users', true, 303);
    }
}