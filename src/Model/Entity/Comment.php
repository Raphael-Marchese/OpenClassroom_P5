<?php

declare(strict_types=1);

namespace App\Model\Entity;

class Comment
{

    public int $id;

    public function __construct(
        public string $content,
        public \DateTimeInterface $createdAt,
        public \DateTimeInterface $updatedAt,
        public string $status,
        public User $author,
        public BlogPost $blogPost,
    ) {
    }

    public function setId(int $id): void
    {
        if (!isset($this->id)) {
            $this->id = $id;
        }
    }

}
