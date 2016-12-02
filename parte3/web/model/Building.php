<?php

require_once 'core/Model.php';

/**
 * The Building class with simple ORM support.
 */
class Building extends Model
{

  /**
   * @var string The address of the building.
   */
  private $address;

  /**
   * Building constructor.
   * @param string $address The address of the building.
   */
  public function __construct($address)
  {
    $this->address = $address;
  }

  /**
   * Finds a building in the database.
   * @param $address string The address of the building to find.
   * @return null|Building The building found. null if doesn't exist.
   */
  public static function find($address)
  {
    try {
      $stmt = self::$connection->prepare('SELECT * FROM edificio WHERE morada = :morada');
      $stmt->bindValue(':morada', $address);
      $stmt->execute();
      if ($stmt->rowCount() == 0) return null;
      $row = $stmt->fetch();
      return new Building($row['morada']);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns all buildings in the database.
   * @return Building[] All of the buildings in the database.
   */
  public static function all()
  {
    $buildings = [];
    try {
      $stmt = self::$connection->prepare('SELECT * FROM edificio ORDER BY morada');
      $stmt->execute();
      foreach ($stmt->fetchAll() as $row) {
        array_push($buildings, new Building($row['morada']));
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $buildings;
  }

  /**
   * Creates a new building in the database.
   * @param $address string The address of the building.
   * @return Building|null Building instance if created successfully. null if a Building with the
   * given address already exists.
   */
  public static function create($address)
  {
    try {
      $stmt = self::$connection->prepare('INSERT INTO edificio(morada) VALUES(:morada)');
      $stmt->bindParam(':morada', $address);
      if ($stmt->execute())
        return new Building($address);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Deletes the building and all its workspaces from the database.
   * @return bool true if the building was deleted successfully. false if it doesn't exist.
   */
  public function delete()
  {
    try {
      // Remove the building
      $stmt = self::$connection->prepare('DELETE FROM edificio WHERE morada = :morada');
      $stmt->bindValue(':morada', $this->getAddress());
      return $stmt->execute();
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return false;
  }

  /**
   * Returns all workspaces in the database of this building.
   * @return Workspace[] All workspaces in the building.
   */
  public function getWorkspaces()
  {
    $workspaces = [];
    try {
      require_once 'Workspace.php';
      $stmt = self::$connection->prepare('SELECT * FROM espaco WHERE morada = :morada ORDER BY codigo');
      $stmt->bindValue(':morada', $this->getAddress());
      $stmt->execute();
      foreach ($stmt->fetchAll() as $row) {
        $workspace = new Workspace(
            $this,
            $row['codigo']
        );
        array_push($workspaces, $workspace);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $workspaces;
  }

  /**
   * Returns the address of the building.
   * @return string The address of the building.
   */
  public function getAddress()
  {
    return $this->address;
  }

}