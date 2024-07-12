<?php
declare(strict_types=1);

namespace App\Entity;

class Comment
{
    private int $id;
    private string $content;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;
    private User $author;
    private BlogPost  $blogPost;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return void
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     * @return void
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return void
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return BlogPost
     */
    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    /**
     * @param BlogPost $blogPost
     * @return void
     */
    public function setBlogPost(BlogPost $blogPost): void
    {
        $this->blogPost = $blogPost;
    }
}