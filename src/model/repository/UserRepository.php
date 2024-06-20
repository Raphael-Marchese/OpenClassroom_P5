<?php
declare(strict_types=1);

namespace App\model\repository;

use App\entity\User;
use App\model\Database;
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
        $query = 'INSERT INTO user (username, first_name, last_name, email, password, role) VALUES (:username, :firstName, :lastName, :email, :password, :role)';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':username', $user->username, type: PDO::PARAM_STR);
        $statement->bindValue(':password', $user->password, type: PDO::PARAM_STR);
        $statement->bindValue(':email', $user->email, type: PDO::PARAM_STR);
        $statement->bindValue(':firstName', $user->firstName, type: PDO::PARAM_STR);
        $statement->bindValue(':lastName', $user->lastName, type: PDO::PARAM_STR);
        $statement->bindValue(':role', 'ROLE_USER');


        if ($statement->execute()) {
            return true;
        } else {
            // Vous pouvez enregistrer les erreurs dans un fichier log ou gérer les erreurs de manière appropriée
            error_log('Erreur lors de l\'insertion de l\'utilisateur: ' . implode(', ', $stmt->errorInfo()));
            return false;
        }
    }

    public function findByEmail(string $email): bool | null |array
    {
        $query = 'SELECT * FROM user WHERE email = :email';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifier s'il y a des résultats
        if ($result !== false) {
            return $result;
        }

        return null; // Aucun résultat trouvé
    }

    public function findById(?int $id): ?User
    {
        $query = 'SELECT * FROM user WHERE id = :id';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifier s'il y a des résultats
        if ($result !== false) {
            $user = new User(
                $result['first_name'],
                $result['last_name'],
                $result['username'],
                $result['email'],
                $result['password'],
                $result['role']
            );
            $user->setId($result['id']);
            return $user;
        }

        return null; // Aucun résultat trouvé
    }
}
