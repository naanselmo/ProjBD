<?php

require_once 'core/Model.php';

class User extends Model
{

  /**
   * @var string The nif of the user.
   */
  private $nif;

  /**
   * @var string The name of the user.
   */
  private $name;

  /**
   * @var string The phone number of the user.
   */
  private $phone;

  /**
   * User constructor.
   * @param string $nif The nif of the user.
   * @param string $name The name of the user.
   * @param string $phone The phone number of the user.
   */
  public function __construct($nif, $name, $phone)
  {
    $this->nif = $nif;
    $this->name = $name;
    $this->phone = $phone;
  }

  /**
   * Finds a user in the database with a given nif.
   * @param $nif string The nif of the user to search.
   * @return null|User null if the user doesn't exist. Otherwise a user instance.
   */
  public static function find($nif)
  {
    try {
      $stmt = self::$connection->prepare('SELECT * FROM user WHERE user.nif = :nif');
      $stmt->bindValue(':nif', $nif);
      $stmt->execute();
      $stmt->execute();
      if ($stmt->rowCount() == 0) return null;
      $row = $stmt->fetch();
      return new User($row['nif'], $row['nome'], $row['telefone']);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns the nif of the user.
   * @return string The nif of the user.
   */
  public function getNif()
  {
    return $this->nif;
  }

  /**
   * Returns the name of the user.
   * @return string The name of the user.
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Returns the phone number of the user.
   * @return string The phone number of the user.
   */
  public function getPhone()
  {
    return $this->phone;
  }

}