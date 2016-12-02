<?php

require_once 'Database.php';

abstract class Model
{
  /**
   * @var Database The instance to the database class.
   */
  protected static $database;

  /**
   * @var PDO The connection to the database.
   */
  protected static $connection;

  /**
   * Initializes all static fields as a static Java block would.
   */
  static function init()
  {
    self::$database = Database::getDatabase();
    self::$connection = self::$database->getConnection();
  }

}

// Init static like static block codes do in Java
Model::init();