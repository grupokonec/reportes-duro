<?php

namespace App\Models;

use PDO;
use PDOException;

class Connection
{
    private $host;
    private $db;
    private $user;
    private $password;
    private $pdo;

    public function __construct()
    {
        $this->host = $_ENV['HOST'];
        $this->db = $_ENV['DB'];
        $this->user = $_ENV['USER'];
        $this->password = $_ENV['PASS'];


        try {
            $this->pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db,
                $this->user,
                $this->password
            );

            $this->pdo->exec('set names utf8');
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }


    public function queryExe($query, $params = [])
    {

        try {
            $stmt = $this->pdo->prepare($query);
            // return $stmt;
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
}
