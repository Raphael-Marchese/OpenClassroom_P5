<?php
declare(strict_types=1);

namespace App\entity;

use Cassandra\Date;

class BlogPost
{

    public function __construct(
        public string $title,
        public string $chapo,
        public \DateTimeInterface $createdAt = new \DateTimeImmutable(),
        public ?\DateTimeInterface $updatedAt = null,
        public string $content,
        public ?string $image,
        public string $status,
        public User $author,
    )
    {
    }

    public readonly int $id;

}