<?php 

namespace Config;

use Dotenv\Dotenv;
use PDO;
use PDOException;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '../../');
$dotenv->load();

class ConnectionDatabase
{
    private $host;
    private $dbname;
    private $user;
    private $pass;

    private $pdo;

    public function __construct()
    {
        $this->host   = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->user   = $_ENV['DB_USER'];
        $this->pass   = $_ENV['DB_PASS'];

        $this->openConnect();
    }

    public function openConnect()
    {
        try {
            $connection_string = "pgsql:host=$this->host;dbname=$this->dbname";

            $this->pdo = new PDO($connection_string, $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $this->pdo;
        } catch (PDOException $e) {
            echo "Connection refused." . $e->getMessage();
        }
    }

    public function closeConnect()
    {
        $this->pdo = null;
    }
}

