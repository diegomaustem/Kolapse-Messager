<?php 

namespace App\Repositories;

interface PaymentRepositoryInterface {
    public function listPayments(): array;
    public function makePayment(array $payment): bool; 
}