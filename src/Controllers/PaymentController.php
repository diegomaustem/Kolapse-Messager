<?php
namespace App\Controllers;

use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Services\RabbitMQService;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentController 
{
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function listPayments(Request $request, Response $response)
    {
        try {
            $payments = $this->paymentRepository->listPayments();
            $response->getBody()->write(json_encode($payments));

            return $response->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = array("message" => $e->getMessage());

            $response->getBody()->write(json_encode($error));
        
            return $response
              ->withHeader('content-type', 'application/json')
              ->withStatus(500);
        }
    }

    public function makePayment(Request $request, Response $response) 
    {
        $data = json_decode($request->getBody(), true);

        $this->callRabbitMQService($data);die();

        try {
            $resultQuery = $this->paymentRepository->makePayment($data);
            if($resultQuery === true) {
                // TESTE RABBITMQ - HERE

                // $this->callRabbitMQService($payerEmail);

                // END TESTE RABBITMQ - HERE
                $response->getBody()->write("Payment entered successfully!");
                    return $response
                        ->withHeader('content-type', 'application/json')
                        ->withStatus(200);
            }
        } catch(PDOException $e) {
            $error = array(
                "message" => $e->getMessage()
            );
                 
            $response->getBody()->write(json_encode($error));
                return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(500);
        }
    }

    private function callRabbitMQService($data)
    {
        $sendingData = [
            'message' => "Your payment has been made!",
            'value' => $data['value'],
            'status' => $data['status'],
        ];
        $rabbitMQService = new RabbitMQService(json_encode($sendingData));
    }
}