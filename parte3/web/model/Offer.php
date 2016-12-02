<?php

require_once 'core/Model.php';

class Offer extends Model
{

  /**
   * @var Rentable The rentable the offer is on.
   */
  private $rentable;

  /**
   * @var DateTime The date when the offer started.
   */
  private $startDate;

  /**
   * @var DateTime The date when the offer ends.
   */
  private $endDate;

  /**
   * @var double The cost of the offer.
   */
  private $price;

  /**
   * Offer constructor.
   * @param Rentable $rentable The rentable the offer is on.
   * @param DateTime $startDate The date when the offer started.
   * @param DateTime $endDate The date when the offer ends.
   * @param float $price The price of the offer.
   */
  public function __construct(Rentable $rentable, DateTime $startDate, DateTime $endDate, $price)
  {
    $this->rentable = $rentable;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
    $this->price = $price;
  }

  /**
   * Returns a offer from the database with the give key.
   * @param $address string The address of the rentable.
   * @param $code string The code of the rentable.
   * @param $startDate DateTime The start date of the offer.
   * @return null|Offer null if no offer is found. Otherwise a offer instance.
   */
  public static function find($address, $code, $startDate)
  {
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM oferta WHERE morada = :morada AND codigo = :codigo AND data_inicio = :data_inicio'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->bindValue(':data_inicio', Database::formatDate($startDate));
      $stmt->execute();
      if ($stmt->rowCount() == 0) return null;
      $row = $stmt->fetch();
      require_once 'Rentable.php';
      return new Offer(
          Rentable::find($row['morada'], $row['codigo']),
          Database::parseDate($row['data_inicio']),
          Database::parseDate($row['data_fim']),
          $row['tarifa']
      );
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Creates the offer in the database.
   * @param $address string The address of the building where the rentable is.
   * @param $code string The code of the rentable.
   * @param $startDate DateTime The start date of the offer.
   * @param $endDate DateTime The end date of the offer.
   * @param $price float The price of the offer.
   * @return null|Offer Offer instance if created successfully. null if the start_date intersects with any
   * offer for the same rentable.
   */
  public static function create($address, $code, $startDate, $endDate, $price)
  {
    try {
      $stmt = self::$connection->prepare(
          'INSERT INTO oferta(morada, codigo, data_inicio, data_fim, tarifa) VALUES (:morada, :codigo, :data_inicio, :data_fim, :tarifa)'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->bindValue(':data_inicio', Database::formatDate($startDate));
      $stmt->bindValue(':data_fim', Database::formatDate($endDate));
      $stmt->bindValue(':tarifa', $price);
      if ($stmt->execute()) {
        require_once 'Rentable.php';
        return new Offer(Rentable::find($address, $code), $startDate, $endDate, $price);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns all the available offers.
   * An available offer is a offer which has either no reservations at all or
   * all the reservations it has are different than accepted or paid.
   * @return Offer[] All the available offers.
   */
  public static function allAvailable()
  {
    $offers = [];
    try {
      $stmt = self::$connection->prepare(
          'SELECT o.morada, o.codigo, o.data_inicio, o.data_fim, o.tarifa
                    FROM oferta o LEFT OUTER JOIN (
                      SELECT morada, codigo
                      FROM aluga NATURAL JOIN (
                        SELECT numero
                        FROM estado e NATURAL JOIN (
                          SELECT numero, MAX(time_stamp) AS time_stamp
                          FROM estado
                          GROUP BY numero
                        ) f
                        WHERE estado = \'Aceite\' OR estado = \'Paga\'
                      ) z
                    ) s
                    ON o.morada = s.morada
                    AND o.codigo = s.codigo
                    WHERE s.codigo IS NULL ORDER BY data_inicio DESC'
      );
      $stmt->execute();
      require_once 'Rentable.php';
      foreach ($stmt->fetchAll() as $row) {
        $offer = new Offer(
            Rentable::find($row['morada'], $row['codigo']),
            Database::parseDate($row['data_inicio']),
            Database::parseDate($row['data_fim']),
            $row['tarifa']
        );
        array_push($offers, $offer);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $offers;
  }

  /**
   * Returns all offers that are over rentables that are leased by the user with the given nif.
   * @param $nif string The nif of the user.
   * @return Offer[] All offers that are over rentables that are leased by the user with the given nif.
   */
  public static function allFrom($nif)
  {
    $offers = [];
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM oferta NATURAL JOIN arrenda WHERE nif = :nif ORDER BY data_inicio DESC'
      );
      $stmt->bindValue(':nif', $nif);
      $stmt->execute();
      require_once 'Rentable.php';
      foreach ($stmt->fetchAll() as $row) {
        $offer = new Offer(
            Rentable::find($row['morada'], $row['codigo']),
            Database::parseDate($row['data_inicio']),
            Database::parseDate($row['data_fim']),
            $row['tarifa']
        );
        array_push($offers, $offer);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $offers;
  }

  /**
   * Returns the end date of the offer.
   * @return DateTime The end date of the offer.
   */
  public function getEndDate()
  {
    return $this->endDate;
  }

  /**
   * Returns the price of the offer.
   * @return float The price of the offer.
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * Does a reservation to this offer.
   * @param $user User The user who did the reservation.
   * @param $number string The number of the reservation.
   * @return null|Reservation null if created successfully. Otherwise an reservation instance.
   */
  public function reserve($user, $number)
  {
    require_once 'Reservation.php';
    return Reservation::create(
        $this->getRentable()->getBuilding()->getAddress(),
        $this->getRentable()->getCode(),
        $this->getStartDate(),
        $user->getNif(),
        $number
    );
  }

  /**
   * Returns the rentable the offer is on.
   * @return Rentable The rentable the offer is on.
   */
  public function getRentable()
  {
    return $this->rentable;
  }

  /**
   * Returns the start date of the offer.
   * @return DateTime The start date of the offer.
   */
  public function getStartDate()
  {
    return $this->startDate;
  }

  /**
   * Deletes the offer from the database.
   * @return bool true if successfully deleted the offer. false otherwise.
   */
  public function delete()
  {
    try {
      $stmt = self::$connection->prepare(
          'DELETE FROM oferta WHERE morada = :morada AND codigo = :codigo AND data_inicio = :data_inicio'
      );
      $stmt->bindValue(':morada', $this->getRentable()->getBuilding()->getAddress());
      $stmt->bindValue(':codigo', $this->getRentable()->getCode());
      $stmt->bindValue(':data_inicio', Database::formatDate($this->getStartDate()));
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return false;
  }

}