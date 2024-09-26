<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Exception\DatabaseException;
use Database\Database;
use App\Model\Entity\User;

use PDO;
use PDOStatement;

class UserRepository extends Database
{
    /**
     * @throws DatabaseException
     */
    public function findAll(): bool|PDOStatement
    {
        $result = $this->connect()->query('SELECT * FROM user ORDER BY id ASC');

        if ($result === false) {
            throw new DatabaseException('le chargement a échoué');
        }

        return $result;
    }

    /**
     * @throws DatabaseException
     */
    public function save(User $user): bool
    {
        $query = 'INSERT INTO user (username, first_name, last_name, email, password, role) VALUES (:username, :firstName, :lastName, :email, :password, :role)';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':username', $user->username);
        $statement->bindValue(':password', $user->password);
        $statement->bindValue(':email', $user->email);
        $statement->bindValue(':firstName', $user->firstName);
        $statement->bindValue(':lastName', $user->lastName);
        $statement->bindValue(':role', 'ROLE_USER');


        if (!$statement->execute()) {
            throw new DatabaseException('Erreur lors de la création du compte');
        }

        return true;
    }

    public function findByEmail(string $email): bool|null|array
    {
        $query = 'SELECT * FROM user WHERE email = :email';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Vérifier s'il n'y a aucun résultat
        if ($result === false) {
            return null;
        }

        return $result; // Renvoie les résultats
    }

    public function findById(?int $id): ?User
    {
        $query = 'SELECT * FROM user WHERE id = :id';
        $statement = $this->connect()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        // S'il y a un résultat
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
}
