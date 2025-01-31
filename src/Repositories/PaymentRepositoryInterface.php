<?php 

namespace App\Repositories;

interface PaymentRepositoryInterface {
    public function listPayments();
    public function makePayment(array $payment): bool; 
}