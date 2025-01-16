<?php 

namespace App\Services;

use Config\ConnectionRBMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private $rabbitMQConnectionInstance;
    private $channelRBMQ;
    private $queueName = 'payments';

    public function __construct()
    {
        $this->rabbitMQConnectionInstance = new ConnectionRBMQ();

        $this->rabbitMQConnectionInstance->declareQueue($this->queueName);

        $this->channelRBMQ = $this->rabbitMQConnectionInstance->getChannel();
    }

    public function sendMessage($message, $queueName) 
    {
        // Declarar a fila
        $this->channelRBMQ->queue_declare($queueName, false, true, false, false);
        
        // Criar a mensagem
        $msg = new AMQPMessage($message);
        
        // Enviar a mensagem
        $this->channelRBMQ->basic_publish($msg, '', $queueName);
    }
}