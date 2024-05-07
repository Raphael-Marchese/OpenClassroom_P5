<?php
declare(strict_types=1);

namespace App\entity;

class Comment
{
    private int $id;
    private string $content;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private User $author;
    private BlogPost  $blogPost;
    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
    public function getAuthor(): User
    {
        return $this->author;
    }
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }
    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }
    public function setBlogPost(BlogPost $blogPost): void
    {
        $this->blogPost = $blogPost;
    }
}