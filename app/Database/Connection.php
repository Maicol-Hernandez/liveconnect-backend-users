<?php

namespace App\Database;

use PDO;
use App\Response;
use PDOException;

class Connection extends PDO
{

    private string $host, $user, $pass, $dbname, $dbtype = "";

    public function __construct()
    {
        $this->dbname = $_ENV['DB_NAME'];
        $this->user = $_ENV['DB_USER'];
        $this->pass = $_ENV['DB_PASSWORD'];
        $this->host = $_ENV['DB_HOST'];
        $this->dbtype = $_ENV['DB_TYPE'];

        try {
            // connection
            $dns = "{$this->dbtype}:dbname={$this->dbname};host={$this->host};charset=utf8";

            parent::__construct($dns, $this->user, $this->pass, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

            ]);
        } catch (PDOException $e) {
            //throw $th;
            $response = new Response('json', "An error has ocurred and cannot connect to the database:{$e->getMessage()}", 503);
            $response->returnData();
            exit;
        }
    }
}
