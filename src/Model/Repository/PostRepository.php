<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Exception\DatabaseException;
use Database\Database;
use App\Model\Entity\BlogPost;
use App\Service\PostFactory;
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
     * @throws DatabaseException
     */
    public function findAll(): bool|PDOStatement
    {
        $result = $this->connect()->query('SELECT * FROM blog_post ORDER BY updated_at ASC');

        if ($result === false) {
            throw new DatabaseException('le chargement a échoué');
        }

        return $result;
    }

    /**
     * Find one post by id
     * @param int $id
     * @return BlogPost|null
     */
    public function findById(int $id): ?BlogPost
    {
        $query = 'SELECT * FROM blog_post WHERE id = :id';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifier s'il y a des résultats
        if ($result === false) {
            return null; // Aucun résultat trouvé
        }

        $blogPost = $this->postFactory->createBlogPost($result);
        $blogPost->id = $result['id'];

        return $blogPost;
    }

    /**
     * @param BlogPost $blogPost
     * @return bool
     * @throws DatabaseException
     */
    public function create(BlogPost $blogPost): bool
    {
        $query = 'INSERT INTO blog_post (title, chapo, created_at, updated_at, content, status, author, image) VALUES (:title, :chapo, :createdAt, :updatedAt, :content, :status, :author, :image)';
        $db = $this->connect();
        $statement = $db->prepare($query);
        $statement->bindValue(':title', $blogPost->title, type: PDO::PARAM_STR);
        $statement->bindValue(':chapo', $blogPost->chapo, type: PDO::PARAM_STR);
        $statement->bindValue(':createdAt', $blogPost->createdAt->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':updatedAt', $blogPost->updatedAt?->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':status', $blogPost->status, type: PDO::PARAM_STR);
        $statement->bindValue(':content', $blogPost->content, type: PDO::PARAM_STR);
        $statement->bindValue(':author', $blogPost->author->id, type: PDO::PARAM_INT);
        $statement->bindValue(':image', $blogPost->image, type: PDO::PARAM_STR);

        if (!$statement->execute()) {
            throw new DatabaseException('erreur BDD lors de la création du post');
        }

        $blogPost->id = (int)$db->lastInsertId();

        return true;
    }

    /**
     * @param BlogPost $blogPost
     * @param int $id
     * @return bool
     *
     */
    public function update(BlogPost $blogPost, int $id): bool
    {
        $query = 'UPDATE blog_post SET title = :title, chapo = :chapo, updated_at = :updatedAt, content = :content, status = :status, image = :image WHERE id = :id';
        $db = $this->connect();
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':title', $blogPost->title, type: PDO::PARAM_STR);
        $statement->bindValue(':chapo', $blogPost->chapo, type: PDO::PARAM_STR);
        $statement->bindValue(':updatedAt', $blogPost->updatedAt->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':status', $blogPost->status, type: PDO::PARAM_STR);
        $statement->bindValue(':content', $blogPost->content, type: PDO::PARAM_STR);
        $statement->bindValue(':image', $blogPost->image, type: PDO::PARAM_STR);


        if (!$statement->execute()) {
            throw new DatabaseException('erreur BDD lors de la mise à jour du post');
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws bool|DatabaseException
     */
    public function delete(int $id): bool
    {
        $query = 'DELETE FROM blog_post WHERE id = :id';
        $db = $this->connect();
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        if (!$statement->execute()) {
            throw new DatabaseException('erreur BDD lors de la suppression du post');
        }

        return true;
    }
}
