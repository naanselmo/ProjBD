<?php

require_once 'core/Model.php';

class Rentable extends Model
{
  /**
   * @var Building The building where the rentable is.
   */
  protected $building;

  /**
   * @var string The unique code of the rentable.
   */
  protected $code;

  /**
   * @var string The imaged url of the rentable.
   */
  protected $image;

  /**
   * @var User The user who is leasing the rentable.
   */
  protected $user;

  /**
   * Rentable constructor.
   * @param Building $building The building where the rentable is.
   * @param string $code The unique code of the rentable.
   * @param string $image The image url of the rentable.
   * @param $user User The user who is leasing the place.
   */
  public function __construct(Building $building, $code, $image = null, $user = null)
  {
    $this->building = $building;
    $this->code = $code;
    $this->image = $image;
    $this->getImage();
    $this->getLeaser();
  }

  /**
   * Returns the image url of the rentable.
   * @return string The image url of the rentable.
   */
  public function getImage()
  {
    if ($this->image == null)
      $this->fetchImage();
    return $this->image;
  }

  /**
   * Fetches from the database the image url of the rentable.
   */
  public function fetchImage()
  {
    if ($this->image == null) {
      try {
        $stmt = self::$connection->prepare(
            'SELECT foto FROM alugavel WHERE morada = :morada AND codigo = :codigo'
        );
        $stmt->bindValue(':morada', $this->getBuilding()->getAddress());
        $stmt->bindValue(':codigo', $this->getCode());
        $stmt->execute();
        $this->image = $stmt->fetch()['foto'];
      } catch (PDOException $e) {
        error_log($e->getMessage());
        error_log($e->getTraceAsString());
      }
    }
  }

  /**
   * Returns the building where the rentable is.
   * @return Building The building where the rentable is.
   */
  public function getBuilding()
  {
    return $this->building;
  }

  /**
   * Returns the unique code of the rentable.
   * @return string The unique code of the rentable.
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Returns the user who leases the rentable.
   * @return User The user who leases the rentable.
   */
  public function getLeaser()
  {
    if ($this->user == null)
      $this->fetchUser();
    return $this->user;
  }

  /**
   * Fetches the user from the database.
   */
  public function fetchUser()
  {
    if ($this->user == null) {
      try {
        $stmt = self::$connection->prepare(
            'SELECT nif FROM arrenda WHERE morada = :morada AND codigo = :codigo'
        );
        $stmt->bindValue(':morada', $this->getBuilding()->getAddress());
        $stmt->bindValue(':codigo', $this->getCode());
        $stmt->execute();
        require_once 'User.php';
        $this->user = User::find($stmt->fetch()['nif']);
      } catch (PDOException $e) {
        error_log($e->getMessage());
        error_log($e->getTraceAsString());
      }
    }
  }

  /**
   * Returns all rentables in the database.
   * @return Rentable[] All rentables in the database.
   */
  public static function all()
  {
    $rentables = [];
    try {
      $stmt = self::$connection->prepare('SELECT * FROM alugavel NATURAL JOIN arrenda ORDER BY morada, codigo');
      $stmt->execute();
      require_once 'Building.php';
      require_once 'User.php';
      foreach ($stmt->fetchAll() as $row) {
        $rentable = new Rentable(
            new Building($row['morada']),
            $row['codigo'],
            $row['foto'],
            User::find($row['nif'])
        );
        array_push($rentables, $rentable);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $rentables;
  }

  /**
   * Returns all rentables that are leased by the user with the given nif.
   * @param $nif string The nif of the user who leased the rentables.
   * @return Rentable[] All rentables that are leased by the user with the given nif.
   */
  public static function allFrom($nif)
  {
    $rentables = [];
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM alugavel NATURAL JOIN arrenda WHERE nif = :nif ORDER BY morada, codigo'
      );
      $stmt->bindValue(':nif', $nif);
      $stmt->execute();
      require_once 'Building.php';
      require_once 'User.php';
      foreach ($stmt->fetchAll() as $row) {
        $rentable = new Rentable(
            new Building($row['morada']),
            $row['codigo'],
            $row['foto'],
            User::find($row['nif'])
        );
        array_push($rentables, $rentable);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $rentables;
  }

  /**
   * @param $address string The address of the building where the rentable is.
   * @param $code string The code of the rentable.
   * @return null|Rentable null if rentable doesn't exist. Otherwise a rentable instance.
   */
  public static function find($address, $code)
  {
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM alugavel NATURAL JOIN arrenda WHERE morada = :morada AND codigo = :codigo'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->execute();
      if ($stmt->rowCount() == 0) return null;
      $row = $stmt->fetch();
      require_once 'Building.php';
      require_once 'User.php';
      return new Rentable(new Building($row['morada']), $row['codigo'], $row['foto'], User::find($row['nif']));
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Creates a new rentable in the database.
   * @param $address string The address where the rentable is.
   * @param $code string The unique code of the rentable.
   * @param $image string The imaged url of the rentable.
   * @param $nif string The nif of the user leasing the rentable.
   * @return null|Rentable Rentable instance if created successfully. null if Rentable with
   * the given address and code already exists.
   */
  public static function create($address, $code, $image, $nif)
  {
    try {
      $stmt = self::$connection->prepare(
          'INSERT INTO alugavel(morada, codigo, foto) VALUES(:morada, :codigo, :foto)'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->bindValue(':foto', $image);
      if (!$stmt->execute())
        return null;
      $stmt = self::$connection->prepare(
          'INSERT INTO arrenda(morada, codigo, nif) VALUES(:morada, :codigo, :nif)'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->bindValue(':nif', $nif);
      if ($stmt->execute()) {
        require_once 'Building.php';
        require_once 'User.php';
        return new Rentable(new Building($address), $code, $image, User::find($nif));
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns all offers in the database to this rentable.
   * @return Offer[] All offers to this rentable.
   */
  public function getOffers()
  {
    $offers = [];
    try {
      $stmt = self::$connection->prepare(
          'SELECT * FROM oferta WHERE morada = :morada AND codigo = :codigo ORDER BY data_inicio DESC'
      );
      $stmt->bindValue(':morada', $this->getBuilding()->getAddress());
      $stmt->bindValue(':codigo', $this->getCode());
      $stmt->execute();
      foreach ($stmt->fetchAll() as $row) {
        $offer = new Offer(
            $this,
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
   * Deletes the rentable from the database.
   * @return bool true if the rentable was deleted successfully. false if it doesn't exist.
   */
  public function delete()
  {
    try {
      $stmt = self::$connection->prepare('DELETE FROM alugavel WHERE morada = :morada AND codigo = :codigo');
      $stmt->bindValue(':morada', $this->getBuilding()->getAddress());
      $stmt->bindValue(':codigo', $this->getCode());
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return false;
  }

}