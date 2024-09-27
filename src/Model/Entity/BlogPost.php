<?php

declare(strict_types=1);

namespace App\Model\Entity;


use DateTimeInterface;

class BlogPost
{

    public int $id;

    public function __construct(
        public string $title,
        public string $chapo,
        public string $content,
        public string $status,
        public DateTimeInterface $createdAt,
        public User $author,
        public ?string $image,
        public ?DateTimeInterface $updatedAt = null,
    ) {
    }

}
