<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

defined('BASEURL') OR exit('No direct script access allowed');

class race_model
{
    /**
     * DB table name
     */
    const TABLE_NAME = 'race';

    /**
     * race not finished
     */
    const NOT_FINISHED = 0;

    /**
     * race finished
     */
    const FINISHED = 1;

    /**
     * @var PDO
     */
    private $dbConn;

    /**
     * race_model constructor.
     */
    public function __construct()
    {
        $this->dbConn = (database_library::getInstance())->getConnection();
    }

    /**
     * get active races
     * @return array
     */
    public function getActiveRaces()
    {
        $stmt = $this->dbConn->query("SELECT `id`, `progress_time`, `finished`, `finish_time` FROM `" . self::TABLE_NAME . "` WHERE finished = " . self::NOT_FINISHED . "");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * insert race
     * @param $finishTime
     * @return bool|string
     */
    public function insert($finishTime)
    {
        $params['finish_time'] = $finishTime;
        $sql = "INSERT INTO `" . self::TABLE_NAME . "` (`finish_time`) VALUES (:finish_time)";
        $stmt = $this->dbConn->prepare($sql);
        $result = $stmt->execute($params);
        if ($result) {
            return $this->dbConn->lastInsertId();
        }
        return false;
    }

    /**
     * update race
     * @param $params
     * @return bool
     */
    public function update($params)
    {
        $sql = "UPDATE `" . self::TABLE_NAME . "` SET progress_time=:progress_time, finished=:finished WHERE id=:id";
        $stmt= $this->dbConn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * get latest races
     * @param $count
     * @return array
     */
    public function getLatest($count)
    {
        $stmt = $this->dbConn->query("SELECT `id`, `progress_time`, `finished`, `finish_time` FROM `" . self::TABLE_NAME . "` WHERE finished = " . self::FINISHED . " ORDER BY `id` DESC LIMIT $count");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}