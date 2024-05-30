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
    public function save(string $username, string $firstName = null, string $lastName = null, string $email, string $plainPassword): bool
    {
        $password = password_hash($plainPassword, PASSWORD_DEFAULT);
        $query = 'INSERT INTO user (username, first_name, last_name, email, password) VALUES (:username, :firstName, :lastName, :email, :password)';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':username', $username, type: PDO::PARAM_STR);
        $statement->bindValue(':password', $password, type: PDO::PARAM_STR);
        $statement->bindValue(':email', $email, type: PDO::PARAM_STR);
        if (null !== $firstName) {
            $statement->bindValue(':firstName', $firstName, type: PDO::PARAM_STR);
        }
        if (null !== $lastName) {
            $statement->bindValue(':lastName', $lastName, type: PDO::PARAM_STR);
        }

        if ($statement->execute()) {
            echo 'ok';
            return true;
        } else {
            // Vous pouvez enregistrer les erreurs dans un fichier log ou gérer les erreurs de manière appropriée
            error_log('Erreur lors de l\'insertion de l\'utilisateur: ' . implode(', ', $stmt->errorInfo()));
            return false;
        }

    }
}