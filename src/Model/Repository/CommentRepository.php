<?php

declare(strict_types=1);

namespace App\Model\Repository;

use Database\Database;
use PDO;

class CommentRepository extends Database
{

    public function findCommentsByPostId(int $postId): array
    {
        $query = 'SELECT * FROM comment WHERE post = :id ORDER BY created_at ASC';
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

}