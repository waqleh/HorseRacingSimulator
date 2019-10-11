<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

defined('BASEURL') OR exit('No direct script access allowed');

/**
 * Class horse_model
 */
class horse_model
{
    /**
     * DB table name
     */
    const TABLE_NAME = 'horse';

    /**
     * @var PDO
     */
    private $dbConn;

    /**
     * horse_model constructor.
     */
    public function __construct()
    {
        $this->dbConn = (database_library::getInstance())->getConnection();
    }

    /**
     * insert horse to DB
     * @param $raceID
     * @param horse_library $horse
     * @return bool|string last insert ID or false
     */
    public function insert($raceID, $horse)
    {
        $params = [
            'race_id' => $raceID,
            'speed' => $horse->getSpeed(),
            'strength' => $horse->getStrength(),
            'endurance' => $horse->getEndurance(),
            'finished_in_seconds' => $horse->getFinishInSeconds(),
        ];
        $sql = "INSERT INTO `" . self::TABLE_NAME . "` (`race_id`, `speed`, `strength`, `endurance`, `finished_in_seconds`) VALUES (:race_id, :speed, :strength, :endurance, :finished_in_seconds);";
        $stmt = $this->dbConn->prepare($sql);
        $result = $stmt->execute($params);
        if ($result) {
            $horseID = $this->dbConn->lastInsertId();
            $horse->setId((int)$horseID);
            return $horseID;
        }
        return false;
    }

    /**
     * insert multi[le horses to DB
     * @param $raceID
     * @param []horse_library $horses
     * @return array horse IDs
     */
    public function insertMultiple($raceID, $horses)
    {
        $horseIDs = [];
        foreach ($horses as $horse) {
            $horseID = $this->insert($raceID, $horse);
            if ($horseID === false) {
                error_log('ERROR - insert multiple horses failed! race ID: ' . $raceID);
            } else {
                $horseIDs[] = (int)$horseID;
            }
        }
        return $horseIDs;
    }

    /**
     * get horses by race ID
     * @param $raceID
     * @return array
     */
    public function getHorsesByRaceID($raceID)
    {
        $params = ['race_id' => $raceID];
        $sql = "SELECT `id`, `speed`, `strength`, `endurance` FROM `" . self::TABLE_NAME . "` WHERE `race_id` = :race_id ORDER BY `finished_in_seconds`";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute($params);
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get top horses by race ID
     * @param $raceID
     * @param $count
     * @return array
     */
    public function getTopHorsesByRaceID($raceID, $count)
    {
        $params = ['race_id' => $raceID];
        $sql = "SELECT `id`, `speed`, `strength`, `endurance` FROM `" . self::TABLE_NAME . "` WHERE `race_id` = :race_id ORDER BY `finished_in_seconds` LIMIT $count";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute($params);
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get top horse
     * @return array
     */
    public function getTopHorse()
    {
        $sql = "SELECT h.`id`, h.`speed`, h.`strength`, h.`endurance`, h.`finished_in_seconds` FROM `" . self::TABLE_NAME . "` h INNER JOIN `" . race_model::TABLE_NAME . "` r ON h.`race_id` = r.`id` WHERE r.`finished` = " . race_model::FINISHED . " ORDER BY h.`finished_in_seconds` LIMIT 1";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }
}