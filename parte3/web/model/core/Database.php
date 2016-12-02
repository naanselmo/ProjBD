<?php

class Database
{

  /**
   * Format constants.
   */
  const DATE_MYSQL = "Y-m-d";
  const TIMESTAMP_MYSQL = "Y-m-d H:i:s";

  /**
   * @var string The hostname where the database is hosted.
   */
  private static $HOST = "localhost";

  /**
   * @var string The user of the database.
   */
  private static $USERNAME = "root";

  /**
   * @var string The password of the user.
   */
  private static $PASSWORD = "";

  /**
   * @var string The name of the database.
   */
  private static $DATABASE = "proj";

  /**
   * @var Database The database singleton instance.
   */
  private static $database;
  /**
   * @var PDO The pdo connection that we will use to do queries.
   */
  private $connection;

  /**
   * Database constructor.
   */
  private function __construct()
  {
    $this->getConnection();
  }

  /**
   * Returns a PDO connection to the database. Singleton instance so it only gets initialized once per connection.
   * @return PDO The connection.
   */
  public function getConnection()
  {
    if ($this->connection == null) {
      $this->connection = new PDO(
          "mysql:host=" . self::$HOST . ";dbname=" . self::$DATABASE . ";charset=utf8",
          self::$USERNAME,
          self::$PASSWORD
      );
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $this->connection;
  }

  /**
   * Formats a DateTime to a database readable DATE field.
   * @param DateTime $date The date to be formatted.
   * @return string The formatted DateTime as a DATE.
   */
  public static function formatDate(DateTime $date)
  {
    return $date->format(Database::DATE_MYSQL);
  }

  /**
   * Parses a database DATE field to a DateTime.
   * @param $date string The date as a string.
   * @return DateTime The parsed DateTime. null if it fails to parse the given date string.
   */
  public static function parseDate($date)
  {
    return DateTime::createFromFormat(Database::DATE_MYSQL, $date);
  }

  /**
   * Formats a DateTime to a database readable TIMESTAMP field.
   * @param DateTime $timestamp The date to be formatted.
   * @return string The formatted DateTime as a TIMESTAMP.
   */
  public static function formatTimestamp(DateTime $timestamp)
  {
    return $timestamp->format(Database::TIMESTAMP_MYSQL);
  }

  /**
   * Parses a database TIMESTAMP field to a DateTime.
   * @param $timestamp string The date as a string.
   * @return DateTime The parsed DateTime. null if it fails to parse the given date string.
   */
  public static function parseTimestamp($timestamp)
  {
    return DateTime::createFromFormat(Database::TIMESTAMP_MYSQL, $timestamp);
  }

  /**
   * Returns an instance to the database.
   * @return Database The database instance. Singleton so there is only one per connection.
   */
  public static function getDatabase()
  {
    if (self::$database === null) {
      self::$database = new Database();
    }
    return self::$database;
  }

  /**
   * Destructs the database.
   */
  function __destruct()
  {
    $this->connection = null;
  }

}