<?php

require_once 'core/Model.php';

class State extends Model
{
  /**
   * @var DateTime The timestamp of the state.
   */
  private $timestamp;

  /**
   * @var string The state.
   */
  private $state;

  /**
   * State constructor.
   * @param DateTime $timestamp The timestamp of the state.
   * @param string $state The state.
   */
  public function __construct(DateTime $timestamp, $state)
  {
    $this->timestamp = $timestamp;
    $this->state = $state;
  }

  /**
   * Creates a new state for the reservation with the given number.
   * @param $number string The number of the reservation to add the state to.
   * @param $timestamp DateTime The timestamp the change of state occurred.
   * @param $state string The description of the state.
   * @return null|State state instance if created successfully. Otherwise null.
   */
  public static function create($number, $timestamp, $state)
  {
    try {
      $stmt = self::$connection->prepare('INSERT INTO estado(numero, time_stamp, estado) VALUES (:numero, :time_stamp, :estado)');
      $stmt->bindValue(':numero', $number);
      $stmt->bindValue(':time_stamp', Database::formatTimestamp($timestamp));
      $stmt->bindValue(':estado', $state);
      if ($stmt->execute())
        return new State($timestamp, $state);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Finds the most recent state for the reservation with the given number.
   * @param $number string The number of the reservation.
   * @return null|State The last state of the reservation with the given number. null if doesn't exist.
   */
  public static function findMostRecent($number)
  {
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM estado WHERE numero = :numero ORDER BY time_stamp DESC LIMIT 1'
      );
      $stmt->bindValue(':numero', $number);
      if ($stmt->execute() && $stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        return new State(Database::parseTimestamp($row['time_stamp']), $row['estado']);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns the timestamp of the state.
   * @return DateTime The timestamp of the state.
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }

  /**
   * Returns the state.
   * @return string The state.
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * Returns true if a state is accepted.
   * @return bool If the state is accepted.
   */
  function isAccepted()
  {
    return $this->state == 'Aceite';
  }

}