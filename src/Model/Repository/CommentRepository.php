<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Exception\DatabaseException;
use App\Model\Entity\Comment;
use Database\Database;
use PDO;

class CommentRepository extends Database
{

    public function findByPostId(int $postId): array
    {
        $query = 'SELECT * FROM comment WHERE post = :id ORDER BY updated_at DESC';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':id', $postId, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Vérifier s'il y a des résultats
        if ($result === false) {
            return []; // Aucun résultat trouvé
        }

        return $result;
    }

    public function create(Comment $comment): bool
    {
        $query = 'INSERT INTO comment (content, created_at, updated_at, status, author, post) VALUES (:content, :createdAt, :updatedAt, :status, :author, :blogPost)';
        $db = $this->connect();
        $statement = $db->prepare($query);
        $statement->bindValue(':content', $comment->content, PDO::PARAM_STR);
        $statement->bindValue(':createdAt', $comment->createdAt->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':updatedAt', $comment->updatedAt->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':status', $comment->status, PDO::PARAM_STR);
        $statement->bindValue(':author', $comment->author->id, PDO::PARAM_INT);
        $statement->bindValue(':blogPost', $comment->blogPost->id, PDO::PARAM_INT);

        if (!$statement->execute()) {
            throw new DatabaseException('erreur BDD lors de la création du post');
        }


        return true;
    }

}