<?php
declare(strict_types=1);

namespace App\Model;

class Post
{
    private ?int $id;
    private string $title;
    private string $subtitle;
    private string $content;
    private ?\DateTimeImmutable $postedAt;

    public function __construct(?int $id, string $title, string $subtitle, string $content, ?\DateTimeImmutable $postedAt = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->content = $content;
        $this->postedAt = $postedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPostedAt(): ?\DateTimeImmutable
    {
        return $this->postedAt;
    }
}
