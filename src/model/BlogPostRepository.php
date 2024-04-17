<?php
declare(strict_types=1);

namespace App\model;

class BlogPostRepository extends Database
{
    public function findAll(): bool|\PDOStatement
    {
        return $this->connect()->query('SELECT * FROM blog_post');
    }
}