<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

defined('BASEURL') OR exit('No direct script access allowed');

/**
 * Class database_library
 */
class database_library
{
    //
    /**
     * @var database_library Hold the class instance.
     */
    private static $instance = null;
    /**
     * @var PDO
     */
    private $conn;

    /**
     * @var string db host
     */
    private $host = 'localhost';
    /**
     * @var string db user
     */
    private $user = 'race';
    /**
     * @var string db password
     */
    private $pass = 'race';
    /**
     * @var string db name
     */
    private $name = 'horse_racing_simulator';

    /**
     * database_library constructor.
     * The db connection is established in the private constructor.
     */
    private function __construct()
    {
        $this->conn = new PDO("mysql:host={$this->host};
    dbname={$this->name}", $this->user, $this->pass,
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
    }

    /**
     * get db instance
     * @return database_library
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new database_library();
        }

        return self::$instance;
    }

    /**
     * get db pdo connection
     * @return PDO
     */
    public function getConnection()
    {
        return $this->conn;
    }
}