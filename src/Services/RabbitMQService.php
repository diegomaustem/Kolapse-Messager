<?php 

namespace App\Services;

use Config\ConnectionRBMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private $rabbitMQConnectionInstance;
    private $channelRBMQ;
    private $sendingData;

    private $queues = [
        'paidUsers_queue',
        'shoppingStores_queue',
        'cardUnit_queue'
    ];

    public function __construct($sendingData)
    {
        $this->sendingData = json_decode($sendingData);

        $this->rabbitMQConnectionInstance = new ConnectionRBMQ();

        $this->channelRBMQ = $this->rabbitMQConnectionInstance->getChannel();

        $this->publishMessages($this->sendingData, $this->queues);

        $this->rabbitMQConnectionInstance->closeConnect();
    }

    public function publishMessages($message, $queueName) 
    {
        foreach($queueName as $queue) {
            $this->channelRBMQ->queue_declare($queue, false, false, false, false);
            
            // Criar a mensagem ::: 
            $msg = new AMQPMessage(json_encode($message));

            // Envio das mensagens ::: 
            foreach(range(1, 100) as $i) {
                $this->channelRBMQ->basic_publish($msg, '', $queue);
            }
        }
    }    
}