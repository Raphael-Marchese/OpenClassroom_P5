<?php
declare(strict_types=1);

namespace App\model;

use App\entity\BlogPost;
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

}