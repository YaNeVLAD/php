<?php
declare(strict_types=1);

namespace App\Model;

class UserTable
{
    //Переменные и конструктор класса
    public function __construct(private \PDO $connection)
    {

    }

    //Публичные методы для вызова из других файлов
    public function saveUserToDatabase(User $user): int
    {
        $query =
            <<<SQL
            INSERT INTO `user` 
                (`first_name`, `last_name`, `middle_name`, `gender`, 
                 `birth_date`, `email`, `phone`, `avatar_path`)
            VALUES (:firstName, :lastName, :middleName, :gender, 
                    :birthDate, :email, :phone, :avatarPath);
        SQL;
        try {
            $statement = $this->connection->prepare($query);
            if ($statement === false) {
                throw new \Exception('INVALID DATABASE REQUEST. FAILED TO INSERT DATA INTO DATABASE.');
            } else {
                $statement->execute([
                    ':firstName' => $user->getFirstName(),
                    ':lastName' => $user->getLastName(),
                    ':middleName' => $user->getMiddleName(),
                    ':gender' => $user->getGender(),
                    ':birthDate' => $user->getBirthDate(),
                    ':email' => $user->getEmail(),
                    ':phone' => $user->getPhone(),
                    ':avatarPath' => null,
                ]);
                $userId = $this->connection->lastInsertId();
            }
        } catch (\Exception $e) {
            echo 'ERROR. ' . $e->getMessage() . "\n";
            die();
        }
        return (int) $userId;
    }

    public function find(int $userId): ?User
    {
        $query =
            <<<SQL
                SELECT `user_id`, `first_name`, `last_name`, `middle_name`, 
                        `gender`, `birth_date`, `email`, `phone`, `avatar_path`  
                FROM `user` WHERE `user_id` = $userId;
            SQL;
        $statement = $this->connection->query($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $this->createUserFromRow($row);
        }
        return null;
    }

    public function findAll(): ?array
    {
        $query =
            <<<SQL
                SELECT `user_id`, `first_name` FROM `user`;
            SQL;
        $statement = $this->connection->query($query);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if ($data === false) {
            throw new \Exception("Failed to view all users");
        }
        return $data;
    }

    public function delete(int $userId): void
    {
        $this->deleteAvatar($userId);
        $query =
            <<<SQL
                DELETE FROM `user` WHERE `user_id` = $userId; 
            SQL;
        $statement = $this->connection->query($query);
        if ($statement === false) {
            throw new \Exception("Failed to delete this user");
        }
    }

    public function updateData(array $data, int $userId): ?User
    {
        $user = $this->createUserFromRow($data);
        $query =
            <<<SQL
                UPDATE `user` 
                SET `first_name` = :firstName, `last_name` = :lastName, `middle_name` = :middleName, 
                    `gender` = :gender, `birth_date` = :birthDate, `email` = :email, 
                    `phone` = :phone, `avatar_path` = :avatarPath  
                WHERE `user_id` = $userId;
            SQL;
        $statement = $this->connection->prepare($query);
        if ($statement === false) {
            throw new \Exception("FAILED to send DataBase request");
        }
        $statement->execute([
            ':firstName' => ($user->getFirstName() === '')
                ? throw new \Exception("ESSENTIAL Field is empty - First Name")
                : $user->getFirstName(),
            ':lastName' => ($user->getLastName() === '')
                ? throw new \Exception("ESSENTIAL Field is empty - Last Name")
                : $user->getLastName(),
            ':middleName' => ($user->getMiddleName() === '')
                ? null
                : $user->getMiddleName(),
            ':gender' => ($user->getGender() === '')
                ? throw new \Exception("ESSENTIAL Field is empty - Gender")
                : $user->getGender(),
            ':birthDate' => ($user->getBirthDate() === '')
                ? throw new \Exception("ESSENTIAL Field is empty - Birth Date")
                : $user->getBirthDate(),
            ':email' => ($user->getEmail() === '')
                ? throw new \Exception("ESSENTIAL Field is empty - Email Adress")
                : $user->getEmail(),
            ':phone' => ($user->getPhone() === '')
                ? null
                : $user->getPhone(),
            ':avatarPath' => $this->getAvatarName($userId),
        ]);
        return $user;
    }

    public function updateAvatar(string $fileName, int $userId): ?string
    {
        $query =
            <<<SQL
                UPDATE `user` SET `avatar_path` = :avatar_path WHERE `user_id` = $userId;
            SQL;
        $statement = $this->connection->prepare($query);
        if ($statement === false) {
            throw new \Exception('INVALID DATABASE REQUEST. FAILED TO INSERT IMAGE INTO DATABASE.');
        }
        $statement->execute([
            ':avatar_path' => $fileName,
        ]);
        return $fileName;
    }

    public function deleteAvatar(int $userId): void
    {
        $avatarName = $this->getAvatarName($userId);
        unlink('uploads/' . $avatarName);
    }

    //Внутренние методы для обработки данных и внутренней логики
    private function getAvatarName(int $userId): ?string
    {
        $query =
            <<<SQL
                SELECT avatar_path FROM user WHERE user_id = $userId;
            SQL;
        $statement = $this->connection->query($query);
        if ($statement === false) {
            throw new \Exception("Failed to find user avatar");
        }
        $avatarName = $statement->fetch(\PDO::FETCH_ASSOC);
        return $avatarName['avatar_path'];
    }

    private function createUserFromRow(array $row): User
    {
        return new User(
            $row['user_id'] ?? null,
            $row['first_name'],
            $row['last_name'],
            $row['middle_name'] ?? null,
            $row['gender'],
            $row['birth_date'] ?? null,
            $row['email'],
            $row['phone'],
            $row['avatar_path'] ?? null,
        );
    }

}