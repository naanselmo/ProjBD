<?php

require_once 'Rentable.php';

class Workstation extends Rentable
{
  /**
   * @var Workspace The workspace that owns the station.
   */
  private $workspace;

  /**
   * Workstation constructor.
   * @param Workspace $workspace The workspace that owns the station.
   * @param string $code The unique code of the workstation.
   */
  public function __construct(Workspace $workspace, $code)
  {
    parent::__construct($workspace->building, $code, null, null);
    $this->workspace = $workspace;
  }

  /**
   * Returns all workstations in the database.
   * @return Workstation[] All workstations in the database.
   */
  public static function all()
  {
    $workstations = [];
    try {
      $stmt = self::$connection->prepare('SELECT * FROM posto ORDER BY morada, codigo');
      $stmt->execute();
      foreach ($stmt->fetchAll() as $row) {
        $workstation = new Workstation(
            new Workspace(new Building($row['morada']), $row['codigo_espaco']),
            $row['codigo']
        );
        array_push($workstations, $workstation);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return $workstations;
  }

  /**
   * @param $address string The address of the building where the workstation is.
   * @param $code string The code of the workstation.
   * @return null|Workstation null if workstation doesn't exist. Otherwise a workstation instance.
   */
  public static function find($address, $code)
  {
    try {
      $stmt = self::$connection->prepare('SELECT * FROM posto WHERE morada = :morada AND codigo = :codigo');
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $code);
      $stmt->execute();
      if ($stmt->rowCount() == 0) return null;
      $row = $stmt->fetch();
      return new Workstation(Workspace::find($address, $row['codigo_espaco']), $row['codigo']);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Creates a new workstation in the database including its rentable.
   * @param string $address The address of the building where the workstation is.
   * @param string $code The unique code of the workstation.
   * @param string $image The image url of the workstation.
   * @param string $nif The user who leases the workstation.
   * @param string $workspaceCode The code of the workspace where the workstation is. Always define it! It's null just to respect the parents create method.
   * @return null|Workstation Workstation instance if created successfully. null if Rentable with
   * the given address and code already exists.
   */
  public static function create($address, $code, $image, $nif, $workspaceCode = null)
  {
    try {
      // Create the rentable associated with the workstation.
      $rentable = parent::create($address, $code, $image, $nif);
      if ($rentable == null)
        return null;
      // Create the workstation.
      $stmt = self::$connection->prepare(
          'INSERT INTO posto(morada, codigo, codigo_espaco) VALUES(:morada, :codigo, :codigo_espaco)'
      );
      $stmt->bindValue(':morada', $address);
      $stmt->bindValue(':codigo', $rentable->getCode());
      $stmt->bindValue(':codigo_espaco', $workspaceCode);
      if ($stmt->execute()) {
        require_once 'Workspace.php';
        return new Workstation(Workspace::find($address, $workspaceCode), $rentable->getCode(), $image);
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      error_log($e->getTraceAsString());
    }
    return null;
  }

  /**
   * Returns the parent workspace.
   * @return Workspace The workspace where this station is.
   */
  public function getWorkspace()
  {
    return $this->workspace;
  }

}