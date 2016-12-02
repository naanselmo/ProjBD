<?php

require_once 'Rentable.php';

class Workspace extends Rentable
{

  /**
   * Workspace constructor.
   * @param Building $building The building where the workspace is.
   * @param string $code The unique code of the workspace.
   */
  public function __construct(Building $building, $code)
  {
    parent::__construct($building, $code, null, null);
  }

  /**
   * Returns all workspaces in the database.
   * @return Workspace[] All workspaces in the database.
   */
  public static function all()
  {
    $workspaces = [];
    try {
      $stmt = self::$connection->prepare('SELECT * FROM espaco ORDER BY morada, codigo');
      $stmt->execute();
      foreach ($stmt->fetchAll() as $row) {
        $workspace = new Workspace(new Building($row['morada']), $row['codigo']);
        array_push($workspaces, $workspace);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $workspaces;
  }

  /**
   * @param $address string The address of the building where the workspace is.
   * @param $code string The code of the workspace.
   * @return null|Workspace null if workspace doesn't exist. Otherwise a workspace instance.
   */
  public static function find($address, $code)
  {
    try {
      $stmt = self::$connection->prepare('SELECT * FROM espaco WHERE morada = :morada AND codigo = :codigo');
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->execute();
      if ($stmt->rowCount() == 0) return null;
      $row = $stmt->fetch();
      return new Workspace(new Building($row['morada']), $row['codigo']);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Creates a new workspace in the database including its rentable.
   * @param string $address The address where the workspace is.
   * @param string $code The unique code of the workspace.
   * @param string $image The image url of the workspace.
   * @param string $nif The nif of the user who leases the workspace.
   * @return null|Workspace Workspace instance if created successfully. null if Rentable with
   * the given address and code already exists.
   */
  public static function create($address, $code, $image, $nif)
  {
    try {
      // Create the rentable associated with the workspace.
      $rentable = parent::create($address, $code, $image, $nif);
      if ($rentable == null)
        return null;
      // Create the workspace.
      $stmt = self::$connection->prepare(
          'INSERT INTO espaco(morada, codigo) VALUES(:morada, :codigo)'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $rentable->getCode());
      if ($stmt->execute())
        return new Workspace(new Building($address), $rentable->getCode());
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns all workstations in the database of this workspace.
   * @return Workstation[] All workstations in the workspace.
   */
  public function getWorkstations()
  {
    $workstations = [];
    try {
      require_once 'Workstation.php';
      $stmt = self::$connection->prepare(
          'SELECT * FROM posto WHERE morada = :morada AND codigo_espaco = :codigo ORDER BY morada, codigo'
      );
      $stmt->bindValue(':morada', $this->getBuilding()->getAddress());
      $stmt->bindValue(':codigo', $this->getCode());
      $stmt->execute();
      foreach ($stmt->fetchAll() as $row) {
        $workstation = Workstation::find($row['morada'], $row['codigo']);
        array_push($workstations, $workstation);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $workstations;
  }

  /**
   * Returns the total amount done by the workspace since forever.
   * @return float The amount done by the workspace since forever.
   */
  public function getTotal()
  {
    try {
      require_once 'Workstation.php';
      $stmt = self::$connection->prepare('
        SELECT
          morada,
          codigo,
          sum(montante)
        FROM ((SELECT
                 morada,
                 codigo_espaco                                  AS codigo,
                 (datediff(data_fim, data_inicio) + 1) * tarifa AS montante
               FROM aluga
                 NATURAL JOIN oferta
                 NATURAL JOIN posto
                 NATURAL JOIN paga
               WHERE codigo_espaco = :codigo
                     AND morada = :morada)
              UNION
              (SELECT
                 morada,
                 codigo,
                 (datediff(data_fim, data_inicio) + 1) * tarifa AS montante
               FROM aluga
                 NATURAL JOIN oferta
                 NATURAL JOIN espaco
                 NATURAL JOIN paga
               WHERE codigo = :codigo
                     AND morada = :morada)) t
        GROUP BY morada, codigo;');
      $stmt->bindValue(':morada', $this->getBuilding()->getAddress());
      $stmt->bindValue(':codigo', $this->getCode());
      if ($stmt->execute() && $stmt->rowCount() > 0)
        return $stmt->fetch()[2];
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return 0;
  }

}