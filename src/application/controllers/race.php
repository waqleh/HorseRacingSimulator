<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

defined('BASEURL') OR exit('No direct script access allowed');

use horse_library as horse;

/**
 * Class race_controller
 */
class race_controller extends base_controller
{
    /**
     * how many horses in a single race
     */
    const HORSES_IN_RACE = 8;

    /**
     * max number of running races
     */
    const MAX_RUNNING_RACES = 3;

    /**
     * race distance
     */
    const RACE_DISTANCE = 1500;

    /**
     * advance in progress race by
     */
    const ADVANCE_RACE_BY = 10;

    /**
     * horse race simulation home page
     * this will load all required data for the the home page view
     */
    public function index()
    {
        $raceModel = new race_model();
        $activeRaces = $raceModel->getActiveRaces();
        $this->getRaceHorses($activeRaces);
        $data['race'] = $activeRaces;
        $data['last_5_races'] = $raceModel->getLatest(5);
        $this->getRaceHorses($data['last_5_races'], true);
        $horseModel = new horse_model();
        $data['top_horse'] = $horseModel->getTopHorse();
        $this->loadView('race', $data);
    }

    /**
     * create a new race and generate its horses if possible
     */
    public function createRace()
    {
        $raceModel = new race_model();
        $activeRaces = $raceModel->getActiveRaces();
        if (count($activeRaces) < self::MAX_RUNNING_RACES) {
            $horseModel = new horse_model();
            // race time
            $race['progress_time'] = 0;
            // race finished
            $race['finished'] = false;
            // race finish time
            $race['finish_time'] = 0;

            $horses = [];
            // generate 8 horses with random stats
            for ($i = 0; $i < self::HORSES_IN_RACE; $i++) {
                $horse = new horse();
                if ($horse->getFinishInSeconds() > $race['finish_time']) {
                    // we already know who will finish first so what is the point in not saving it now to the DB
                    $race['finish_time'] = $horse->getFinishInSeconds();
                }
                $horses[] = $horse;
            }
            $raceID = $raceModel->insert($race['finish_time']);
            if ($raceID === false) {
                error_log('ERROR - create race failed while trying to insert a new race');
            } else {
                $race['id'] = $raceID;
                //save horses
                $horseIDs = $horseModel->insertMultiple($raceID, $horses);
                if ($horseIDs < count($horses)) {
                    error_log('ERROR - not all horses where inserted properly');
                }
            }
        }
        header("Location: " . BASEURL);
        exit;
    }

    /**
     * progress the races
     */
    public function progress()
    {
        $raceModel = new race_model();
        $activeRaces = $raceModel->getActiveRaces();
        $this->getRaceHorses($activeRaces);
        foreach ($activeRaces as $key => $race) {
            // race still going
            $race['progress_time'] = $race['progress_time'] + self::ADVANCE_RACE_BY;
            foreach ($race["horses"] as $key2 => $horse) {
                if ($horse->finishRace()) {
                    continue;
                }
                $horse->setDistanceCovered($race['progress_time']);
                if ($horse->finishRace()) {
                    // this horse finished the race
                    $race["finishers"]++;
                }
            }
            if ($race["finishers"] == count($race["horses"])) {
                //race ended now
                $race['finished'] = race_model::FINISHED;
            }
            $params = [
                'id' => $race['id'],
                'progress_time' => $race['progress_time'],
                'finished' => $race['finished']
            ];
            $result = $raceModel->update($params);
            if (!$result) {
                error_log('ERROR - update race failed! params: ' . json_encode($params));
            }
        }
        header("Location: " . BASEURL);
        exit;
    }

    /**
     * get race horses
     * @param $races
     * @param bool $topThree
     */
    private function getRaceHorses(&$races, $topThree = false)
    {
        $horseModel = new horse_model();
        foreach ($races as $key => $race) {
            $race["finishers"] = 0;
            $race["position"] = [];
            $horses = [];
            if ($topThree) {
                $horsesResult = $horseModel->getTopHorsesByRaceID($race['id'], 3);
            } else {
                $horsesResult = $horseModel->getHorsesByRaceID($race['id']);
            }
            foreach ($horsesResult as $key1 => $horse) {
                $horseLibrary = new horse($horse['id'], $horse['speed'], $horse['strength'], $horse['endurance']);
                $horseLibrary->setDistanceCovered($race['progress_time']);
                if ($horseLibrary->getFinishInSeconds() > $race['finish_time']) {
                    // we already know who will finish first so what is the point in not saving it now to the DB
                    $race['finish_time'] = $horseLibrary->getFinishInSeconds();
                }
                //final position
                $horseLibrary->setPosition(($key1 + 1));
                //current position
                $currentDistanceCovered = $horseLibrary->getDistanceCovered();
                $race["current_position"]['horse_' . $horseLibrary->getId()] = $currentDistanceCovered;
                if ($currentDistanceCovered == self::RACE_DISTANCE) {
                    $race["finishers"]++;
                }
                $horses[] = $horseLibrary;
            }
            arsort($race["current_position"]);
            // resort them by id
            usort($horses, function($a, $b) {
                return $a->getId() <=> $b->getId();
            });
            $race['horses'] = $horses;
            $races[$key] = $race;
        }
    }
}