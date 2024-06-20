<?php
declare(strict_types=1);

namespace App\model\repository;

use App\model\Database;
use App\entity\BlogPost;
use DateTime;
use PDO;
use PDOStatement;

class PostRepository extends Database
{
    /**
     * Return all posts
     * @return bool|PDOStatement
     */
    public function findAll(): bool|PDOStatement
    {
        return $this->connect()->query('SELECT * FROM blog_post ORDER BY updated_at DESC');
    }

    /**
     * Find one post by id
     * @param int $id
     * @return bool|PDOStatement|null
     */
    public function findById(int $id): bool | null |array
    {
        $query = 'SELECT * FROM blog_post WHERE id = :id';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifier s'il y a des résultats
        if ($result !== false) {
            return $result;
        }

        return null; // Aucun résultat trouvé
    }

    public function save(BlogPost $blogPost ): bool
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
}