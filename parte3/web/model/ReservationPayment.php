<?php

require_once 'core/Model.php';

class Payment extends Model
{
  /**
   * @var DateTime The timestamp of the payment.
   */
  private $timestamp;

  /**
   * @var string The method of the payment.
   */
  private $method;

  /**
   * Pay constructor.
   * @param $date DateTime The timestamp of the payment.
   * @param $method string The method of the payment.
   */
  public function __construct($date, $method)
  {
    $this->timestamp = $date;
    $this->method = $method;
  }

  /**
   * Creates a new payment for a reservation with the given state.
   * @param $number string The number of the reservation that will be paid.
   * @param $timestamp DateTime The timestamp of when the reservation was paid.
   * @param $method string The method used to pay the reservation.
   * @return Payment payment instance if created successfully. Otherwise null.
   */
  public static function create($number, $timestamp, $method)
  {
    try {
      $stmt = self::$connection->prepare('INSERT INTO paga(numero, data, metodo) VALUES (:numero, :data, :metodo)');
      $stmt->bindValue(':numero', $number);
      $stmt->bindValue(':data', Database::formatTimestamp($timestamp));
      $stmt->bindValue(':metodo', $method);
      if ($stmt->execute())
        return new Payment($timestamp, $method);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Finds a payment for the reservation with the given number.
   * @param $number string The number of the reservation.
   * @return null|Payment The payment for the reservation. null if doesn't exist.
   */
  public static function find($number)
  {
    try {
      $stmt = self::$connection->prepare('SELECT * FROM paga WHERE numero = :numero');
      $stmt->bindValue(':numero', $number);
      if ($stmt->execute() && $stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        return new Payment(Database::parseTimestamp($row['data']), $row['metodo']);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns the timestamp of the payment.
   * @return DateTime The timestamp of the payment.
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }

  /**
   * Returns the method of the payment.
   * @return string The method of the payment.
   */
  public function getMethod()
  {
    return $this->method;
  }

}