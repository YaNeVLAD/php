<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Post;

class PostTable
{
    private const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    // Извлекает из БД данные поста с указанным ID.
    // Возвращает null, если пост не найден
    public function find(int $id): ?Post
    {
        $query = <<<SQL
        SELECT
            id,
            title,
            subtitle,
            content,
            posted_at
        FROM post
        WHERE id = $id
        SQL;

        $statement = $this->connection->query($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            return $this->createPostFromRow($row);
        }

        return null;
    }

    // Сохраняет пост в таблицу post, возвращает ID поста.
    public function add(Post $post): int
    {
        $query = <<<SQL
        INSERT INTO post (title, subtitle, content)
        VALUES (:title, :subtitle, :content)
        SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute([
            ':title' => $post->getTitle(),
            ':subtitle' => $post->getSubtitle(),
            ':content' => $post->getContent()
        ]);

        return (int)$this->connection->lastInsertId();
    }

    private function createPostFromRow(array $row): Post
    {
        return new Post(
            (int)$row['id'],
            $row['title'],
            $row['subtitle'],
            $row['content'],
            $this->parseDateTime($row['posted_at'])
        );
    }

    private function parseDateTime(string $value): \DateTimeImmutable
    {
        $result = \DateTimeImmutable::createFromFormat(self::MYSQL_DATETIME_FORMAT, $value);
        if (!$result)
        {
            throw new \InvalidArgumentException("Invalid datetime value '$value'");
        }
        return $result;
    }
}
