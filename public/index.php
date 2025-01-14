<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use \PDO;
use \PDOException;

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
    $response->getBody()->write("API Running");
    return $response;
});

$app->post('/makePayment', function (Request $request, Response $response, $args) {
    $payment = json_decode($request->getBody(), true);
    
    $response->getBody()->write(json_encode($payment));

    return $response;
});


$app->run();