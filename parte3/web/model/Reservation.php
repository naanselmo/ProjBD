<?php

require_once 'core/Model.php';

class Reservation extends Model
{

  /**
   * @var User The user that did the reservation.
   */
  private $user;

  /**
   * @var Offer The offer that this reservation is about.
   */
  private $offer;

  /**
   * @var string The unique number of the reservation.
   */
  private $number;

  /**
   * @var Payment The payment method and date.
   */
  private $payment;

  /**
   * @var State The current state of the reservation.
   */
  private $state;

  /**
   * Reservation constructor.
   * @param User $user The user that did the reservation.
   * @param Offer $offer The user that did the reservation.
   * @param string $number The unique number of the reservation.
   * @param Payment $payment The payment method and date.
   * @param State $state The state of the reservation.
   */
  public function __construct(User $user, Offer $offer, $number, $payment = null, $state = null)
  {
    $this->number = $number;
    $this->user = $user;
    $this->offer = $offer;
    $this->payment = $payment;
    $this->state = $state;
  }

  /**
   * @param $address string The address of the building where the rentable is.
   * @param $code integer The code of the rentable.
   * @param $startDate DateTime The date when the offer started.
   * @param $nif string The nif of the user who did the reservation.
   * @param $number string The number of the reservation.
   * @return null|Reservation null if no reservation is found. Otherwise a reservation instance.
   */
  public static function find($address, $code, $startDate, $nif, $number)
  {
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM aluga WHERE morada = :morada AND codigo = :codigo
                      AND data_inicio = :data_inicio AND nif = :nif AND numero = :numero'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->bindValue(':data_inicio', Database::formatDate($startDate));
      $stmt->bindValue(':nif', $nif);
      $stmt->bindValue(':numero', $number);
      $stmt->execute();
      if ($stmt->rowCount() == 0) return null;
      $row = $stmt->fetch();
      return new Reservation(
          User::find($row['nif']),
          Offer::find($row['morada'], $row['codigo'], Database::parseDate($row['data_inicio'])),
          $row['numero'],
          Payment::find($number),
          State::findMostRecent($number)
      );
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Creates a new reservation in the database.
   * @param $address string The address of the building where the rentable is.
   * @param $code string The code of the rentable.
   * @param $startDate DateTime The start date when the offer started.
   * @param $nif string The nif of the user who did the reservation.
   * @param $number string The number of the reservation.
   * @return null|Reservation reservation instance if created successfully. Otherwise null.
   */
  public static function create($address, $code, $startDate, $nif, $number)
  {
    try {
      // Create reserva
      $stmt = self::$connection->prepare('INSERT INTO reserva(numero) VALUES (:numero)');
      $stmt->bindValue(':numero', $number);
      if (!$stmt->execute())
        return null;
      // Create aluga
      $stmt = self::$connection->prepare(
          'INSERT INTO aluga(morada, codigo, data_inicio, nif, numero) VALUES (:morada, :codigo, :data_inicio, :nif, :numero)'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->bindValue(':data_inicio', Database::formatDate($startDate));
      $stmt->bindValue(':nif', $nif);
      $stmt->bindValue(':numero', $number);
      if (!$stmt->execute())
        return null;
      // Create estado default Pendente
      require_once 'ReservationState.php';
      $state = State::create($number, new DateTime(), 'Pendente');
      if ($state == null)
        return null;
      return new Reservation(
          User::find($nif),
          Offer::find($address, $code, $startDate),
          $number,
          null,
          $state
      );
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Fetches all the reservations done by the user with the given nif.
   * @param $nif string The nif f the user.
   * @return Reservation[] The reservations the user did.
   */
  public static function allFrom($nif)
  {
    $reservations = [];
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM aluga WHERE nif = :nif ORDER BY numero'
      );
      $stmt->bindValue(':nif', $nif);
      $stmt->execute();
      require_once 'ReservationState.php';
      require_once 'ReservationPayment.php';
      require_once 'Offer.php';
      require_once 'User.php';
      foreach ($stmt->fetchAll() as $row) {
        $reservation = new Reservation(
            User::find($row['nif']),
            Offer::find($row['morada'], $row['codigo'], Database::parseDate($row['data_inicio'])),
            $row['numero'],
            Payment::find($row['numero']),
            State::findMostRecent($row['numero'])
        );
        array_push($reservations, $reservation);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $reservations;
  }

  /**
   * Pays the reservation. Also adds a new state to the state history, with the state 'Paga'.
   * @param $number string The number of the reservation.
   * @param $date DateTime The date the reservation was paid.
   * @param $method string The method used to pay the reservation.
   * @return bool true if successfully pays the reservation. false if the reservation doesn't exist.
   */
  public static function pay($number, $date, $method)
  {
    try {
      require_once 'ReservationState.php';
      require_once 'ReservationPayment.php';
      $payment = Payment::create($number, $date, $method);
      if ($payment == null) return null;
      $state = State::create($number, $date, 'Paga');
      return $state != null;
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return false;
  }

  /**
   * Returns the number of the reservation.
   * @return int The number of the reservation.
   */
  public function getNumber()
  {
    return $this->number;
  }

  /**
   * Returns the user who did the reservation.
   * @return User The user who did the reservation.
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Returns the offer that this reservation is about.
   * @return Offer The offer that this reservation is about.
   */
  public function getOffer()
  {
    return $this->offer;
  }

  /**
   * Returns the payment method and date.
   * @return Payment The payment method and date.
   */
  public function getPayment()
  {
    return $this->payment;
  }

  /**
   * Returns if the reservation is or not paid.
   * @return bool If the reservation is or not paid.
   */
  public function isPaid()
  {
    return $this->payment != null;
  }

  /**
   * Returns the state of the reservation.
   * @return State The state of the reservation.
   */
  public function getState()
  {
    return $this->state;
  }

}