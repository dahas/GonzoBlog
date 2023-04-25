<?php

namespace Gonzo\Service;

use Opis\Database\Connection;
use Opis\Database\Database;
use Opis\ORM\EntityManager;
use PDO;

class DbalService {

    private Connection $con;

    public function __construct(private array|null $options = [])
    {
        $this->con = new Connection(
            $_ENV['DB_DSN'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD']
        );

        $this->con->options([
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);

        $this->con->initCommand('SET NAMES UTF8');
    }

    public function getConnection(): Connection
    {
        return $this->con; 
    }

    public function getDatabase(): Database
    {
        return new Database($this->con); 
    }

    public function getEntityManager(): EntityManager
    {
        return new EntityManager($this->con); 
    }
}