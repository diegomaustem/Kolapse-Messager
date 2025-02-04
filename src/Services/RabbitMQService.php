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

    public function __construct($sendingData)
    {
        $this->sendingData = json_decode($sendingData);

        $this->rabbitMQConnectionInstance = new ConnectionRBMQ();

        $this->channelRBMQ = $this->rabbitMQConnectionInstance->getChannel();

        $this->publishMessages($this->sendingData);

        $this->rabbitMQConnectionInstance->closeConnect();
    }

    public function publishMessages($message) 
    {
        // Direct :::  
        // $exchange = 'payment_users_direct';
        // $this->channelRBMQ->exchange_declare($exchange, 'direct', false, true, false);

        // $routes_keys = [
        //     'generate_contract' => 'generate_contract_key',
        //     'notify_payment' => 'notify_payment_key'
        // ];

        // foreach($routes_keys as $key) {
        //     $msg = new AMQPMessage(json_encode($message), $key);

        //     foreach(range(1, 100) as $i) {
        //         $this->channelRBMQ->basic_publish($msg, $exchange, $key);
        //     }
        // }

        // Fanout :::
        $exchange = 'payment_users_fanout';
        $this->channelRBMQ->exchange_declare($exchange, 'fanout', false, true, false);

        $msg = new AMQPMessage(json_encode($message));

        foreach(range(1, 2) as $i) {
            $this->channelRBMQ->basic_publish($msg, $exchange);
        }  
    }    
}