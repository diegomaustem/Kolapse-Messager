<?php 
declare(strict_types=1);
namespace App\Repositories;

use PDO;
use PDOException;

class PaymentRepository implements PaymentRepositoryInterface
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function listPayments(): array
    {
        $query = "SELECT * FROM payments";

        $statament = $this->connection->query($query);
        $payments = $statament->fetchAll(PDO::FETCH_OBJ);

        return $payments;
    }

    public function makePayment($payment): bool
    {
        $query = "INSERT INTO payments (name, card_number, validity, date, security_code, value, status, email) VALUES (:name, :card_number, :validity, :date, :security_code, :value, :status, :email)";

        $statament  = $this->connection->prepare($query);
        $statament->bindParam(':name', $payment['name']);
        $statament->bindParam(':card_number', $payment['card_number']);
        $statament->bindParam(':validity', $payment['validity']);
        $statament->bindParam(':date', $payment['date']);
        $statament->bindParam(':security_code', $payment['security_code']);
        $statament->bindParam(':value', $payment['value']);
        $statament->bindParam(':status', $payment['status']);
        $statament->bindParam(':email', $payment['email']);

        $resultQuery = $statament->execute();

        return $resultQuery;
    }
}