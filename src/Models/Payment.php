<?php 

namespace App\Models;

class Payment
{
    private $name;
    private $card_number;
    private $validity;
    private $date;
    private $security_code;
    private $value; 
    private $status;
    private $email;

    public function __construct(Payment $payment)
    {
        var_dump($payment['name']);die();
        $this->name = $payment['name'];
        $this->card_number = $payment['card_number'];
        $this->validity = $payment['validity'];
        $this->date = $payment['date'];
        $this->security_code = $payment['security_code'];
        $this->value = $payment['value'];
        $this->status = $payment['status'];
        $this->email = $payment['email'];
    }
}