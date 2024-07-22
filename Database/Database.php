<?php

namespace Database;

use App\Config;
use PDO;


abstract class Database
{
    protected function connect(
        $host = Config::HOST_DATABASE,
        $user = Config::USER_DATABASE,
        $password = Config::PASSWORD_DATABASE,
        $nameDatabase = Config::NAME_DATABASE
    ): PDO {
        try {
            $db = new PDO(
                'mysql:host=' . $host . ';dbname=' . $nameDatabase . ';charset=utf8',
                $user,
                $password
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $db;
        } catch (\Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}
