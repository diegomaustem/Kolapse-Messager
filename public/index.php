<?php

use App\Models\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

/**
  * The routing middleware should be added earlier than the ErrorMiddleware
  * Otherwise exceptions thrown from it will not be handled by the middleware
  */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger  
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/listPayments', function (Request $request, Response $response, $args) {

  $query = "SELECT * FROM payments";

  try{
    $dataBaseConnectionInstance = new Database();

    $connection = $dataBaseConnectionInstance->openConnect();
    $statament = $connection->query($query);
    $payments = $statament->fetchAll(PDO::FETCH_OBJ);

    $dataBaseConnectionInstance->closeConnect();

    $response->getBody()->write(json_encode($payments));

    return $response->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch(PDOException $e) {
    $error = array("message" => $e->getMessage());

    $response->getBody()->write(json_encode($error));

    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});

$app->post('/makePayment', function (Request $request, Response $response, array $args) {
  $data = json_decode($request->getBody(), true);

  $query = "INSERT INTO payments (name, card_number, validity, date, security_code, value, status, email) VALUES (:name, :card_number, :validity, :date, :security_code, :value, :status, :email)";

  try {
    $dataBaseConnectionInstance = new Database();
    $connection = $dataBaseConnectionInstance->openConnect();
    $statament  = $connection->prepare($query);

    $statament->bindParam(':name', $data['name']);
    $statament->bindParam(':card_number', $data['card_number']);
    $statament->bindParam(':validity', $data['validity']);
    $statament->bindParam(':date', $data['date']);
    $statament->bindParam(':security_code', $data['security_code']);
    $statament->bindParam(':value', $data['value']);
    $statament->bindParam(':status', $data['status']);
    $statament->bindParam(':email', $data['email']);

    $resultQuery = $statament->execute();

    $dataBaseConnectionInstance->closeConnect();

    if($resultQuery === true) {
      $response->getBody()->write("Payment entered successfully!");
      return $response
       ->withHeader('content-type', 'application/json')
       ->withStatus(200);
    }
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
});


$app->run();