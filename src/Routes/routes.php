<?php 

namespace App\Routes;

use App\Controllers\PaymentController;
use Slim\App;

return function (App $app) {
    $app->get('/listPayments', [PaymentController::class, 'listPayments']);
    $app->post('/makePayment', [PaymentController::class, 'makePayment']);
};