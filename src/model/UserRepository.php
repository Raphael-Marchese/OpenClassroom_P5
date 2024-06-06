<?php
declare(strict_types=1);

namespace App\model;

use App\entity\User;
use PDO;
use PDOStatement;

class UserRepository extends Database
{
    public function findAll(): bool|PDOStatement
    {
        return $this->connect()->query('SELECT * FROM user ORDER BY id ASC');
    }
    public function save(User $user): bool
    {
        $query = 'INSERT INTO user (username, first_name, last_name, email, password) VALUES (:username, :firstName, :lastName, :email, :password)';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':username', $user->username, type: PDO::PARAM_STR);
        $statement->bindValue(':password', $user->password, type: PDO::PARAM_STR);
        $statement->bindValue(':email', $user->email, type: PDO::PARAM_STR);
        $statement->bindValue(':firstName', $user->firstName, type: PDO::PARAM_STR);
        $statement->bindValue(':lastName', $user->lastName, type: PDO::PARAM_STR);

        if ($statement->execute()) {
            return true;
        } else {
            // Vous pouvez enregistrer les erreurs dans un fichier log ou gérer les erreurs de manière appropriée
            error_log('Erreur lors de l\'insertion de l\'utilisateur: ' . implode(', ', $stmt->errorInfo()));
            return false;
        }

    }
}
