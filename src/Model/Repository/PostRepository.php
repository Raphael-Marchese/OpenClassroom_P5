<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Database;
use App\Entity\BlogPost;
use App\Model\Service\PostFactory;
use DateTime;
use PDO;
use PDOStatement;

class PostRepository extends Database
{
    private PostFactory $postFactory;

    public function __construct()
    {
        $this->postFactory = new PostFactory();
    }

    /**
     * Return all posts
     * @return bool|PDOStatement
     */
    public function findAll(): bool|PDOStatement
    {
        return $this->connect()->query('SELECT * FROM blog_post ORDER BY updated_at ASC');
    }

    /**
     * Find one post by id
     * @param int $id
     * @return BlogPost
     */
    public function findById(int $id)
    {
        $query = 'SELECT * FROM blog_post WHERE id = :id';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifier s'il y a des résultats
        if ($result !== false) {
            $blogPost = $this->postFactory->createBlogPost($result);
            $blogPost->id = $result['id'];
            return $blogPost;
        }

        return null; // Aucun résultat trouvé
    }

    public function create(BlogPost $blogPost): bool
    {
        $query = 'INSERT INTO blog_post (title, chapo, created_at, updated_at, content, status, author, image) VALUES (:title, :chapo, :createdAt, :updatedAt, :content, :status, :author, :image)';
        $db = $this->connect();
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $blogPost->title , type: PDO::PARAM_STR);
        $statement->bindValue(':chapo', $blogPost->chapo, type: PDO::PARAM_STR);
        $statement->bindValue(':createdAt', $blogPost->createdAt->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':updatedAt', $blogPost->updatedAt?->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':status', $blogPost->status, type: PDO::PARAM_STR);
        $statement->bindValue(':content', $blogPost->content, type: PDO::PARAM_STR);
        $statement->bindValue(':author', $blogPost->author->id, type: PDO::PARAM_INT);
        $statement->bindValue(':image', $blogPost->image, type: PDO::PARAM_STR);

        if ($statement->execute()) {

            $blogPost->id = (int) $db->lastInsertId();

            return true;
        }

        error_log('Erreur lors de la création de l\'article: ' . implode(', ', $stmt->errorInfo()));
        return false;
    }

    public function update(BlogPost $blogPost, int $id): bool
    {
        $query = 'UPDATE blog_post SET title = :title, chapo = :chapo, updated_at = :updatedAt, content = :content, status = :status, image = :image WHERE id = :id';
        $db = $this->connect();
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':title', $blogPost->title , type: PDO::PARAM_STR);
        $statement->bindValue(':chapo', $blogPost->chapo, type: PDO::PARAM_STR);
        $statement->bindValue(':updatedAt', $blogPost->updatedAt->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':status', $blogPost->status, type: PDO::PARAM_STR);
        $statement->bindValue(':content', $blogPost->content, type: PDO::PARAM_STR);
        $statement->bindValue(':image', $blogPost->image, type: PDO::PARAM_STR);

        if ($statement->execute()) {
            return true;
        } else {
            error_log('Erreur lors de la mise à jour de l\'article: ' . implode(', ', $stmt->errorInfo()));
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $query = 'DELETE FROM blog_post WHERE id = :id';
        $db = $this->connect();
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        if ($statement->execute()) {
            return true;
        } else {
            error_log('Erreur lors de la mise à jour de l\'article: ' . implode(', ', $stmt->errorInfo()));
            return false;
        }
    }

}
