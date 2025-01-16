<?php 

namespace Config;

use Dotenv\Dotenv;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '../../');
$dotenv->load();

class ConnectionRBMQ
{
    private $connectionRBMQ;
    private $channelRBMQ;

    private $hostRBMQ;
    private $portRBMQ;
    private $userRBMQ; 
    private $passwordRBMQ;

    public function __construct()
    {
        $this->hostRBMQ     = $_ENV['RBMQ_HOST'];
        $this->portRBMQ     = $_ENV['RBMQ_PORT'];
        $this->userRBMQ     = $_ENV['RBMQ_USER'];
        $this->passwordRBMQ = $_ENV['RBMQ_PASS'];

        $this->openConnect();
    }

    public function openConnect()
    {
        try {
            $this->connectionRBMQ = new AMQPStreamConnection($this->hostRBMQ, $this->portRBMQ, $this->userRBMQ, $this->passwordRBMQ);
            $this->channelRBMQ = $this->connectionRBMQ->channel();
        } catch(Exception $e) {
            echo "Error" . $e->getMessage();
        } 
    }

    public function declareQueue($queueName)
    {
        if($this->channelRBMQ) {
            $this->channelRBMQ->queue_declare($queueName, false, true, false, false);
        }
    }

    public function getChannel()
    {
        return $this->channelRBMQ;
    }

    public function closeConnect()
    {
        $this->channelRBMQ->close();
        $this->connectionRBMQ->close();
    }
}