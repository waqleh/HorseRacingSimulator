<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

defined('BASEURL') OR exit('No direct script access allowed');

use race_controller as race;

/**
 * Class horse_library
 */
class horse_library
{
    /**
     * base speed of the horse
     */
    const BASE_SPEED = 5.0;
    /**
     * jockey effect on the hose speed
     */
    const JOCKEY_EFFECT = 5.0;

    /**
     * horse ID
     * @var int
     */
    private $id;
    /**
     * horse speed
     * @var float
     */
    private $speed;
    /**
     * horse strength
     * @var float
     */
    private $strength;
    /**
     * horse endurance
     * @var float
     */
    private $endurance;
    /**
     * distance the horse covered
     * @var float
     */
    private $distanceCovered = 0;
    /**
     * the horse finished the race in seconds
     * @var double
     */
    private $finishInSeconds;
    /**
     * position of the horse in the race
     * @var int
     */
    private $position;

    /**
     * horse constructor.
     * @param int $id
     * @param $speed
     * @param $strength
     * @param $endurance
     */
    public function __construct($id = null, $speed = null, $strength = null, $endurance = null)
    {
        if (is_null($id)) {
            // new horse
            $this->generateHorseStats();
        } else {
            // load horse
            $this->id = $id;
            $this->speed = $speed;
            $this->strength = $strength;
            $this->endurance = $endurance;
        }
        $this->timeToComplete();
    }

    /**
     * getter of distance covered by horse
     * @return float
     */
    public function getDistanceCovered()
    {
        return $this->distanceCovered;
    }

    /**
     * horse finished race in seconds
     * @return float
     */
    public function getFinishInSeconds()
    {
        return $this->finishInSeconds;
    }

    /**
     * get horse speed
     * @return float
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * get horse strength
     * @return float
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * get horse endurance
     * @return float
     */
    public function getEndurance()
    {
        return $this->endurance;
    }

    /**
     * get horse id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * set horse id
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * set final position in the race
     * @param $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * get final position in the race
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * speed after after finishing endurance
     * @return float
     */
    public function newSpeed()
    {
        return $this->bestSpeed() - $this->slowSpeedBy();
    }

    /**
     * how much should the horse slow by after finishing endurance
     * @return float
     */
    private function slowSpeedBy()
    {
        return self::JOCKEY_EFFECT - (self::JOCKEY_EFFECT * ($this->strength * 8 / 100));
    }

    /**
     * speed of the horse before finishing endurance
     * @return float
     */
    private function bestSpeed()
    {
        return self::BASE_SPEED + $this->speed;
    }

    /**
     * how much will the horse travel using the endurance
     * @return float|int
     */
    private function calculateDistanceAtBestSpeed()
    {
        return $this->endurance * 100;
    }

    /**
     * how long will it take the horse to finish the race
     */
    private function timeToComplete()
    {
        //time it would take the horse to complete the race
        $metersAtBestSpeed = $this->calculateDistanceAtBestSpeed();
        $this->finishInSeconds = ($metersAtBestSpeed / $this->bestSpeed()) + ((race::RACE_DISTANCE - $metersAtBestSpeed) / $this->newSpeed());
    }

    /**
     * set distance covered by horse in specific time
     * @param $time
     */
    public function setDistanceCovered($time)
    {
        $timeAtEnduranceEnd = $this->calculateDistanceAtBestSpeed() / $this->bestSpeed();
        if ($timeAtEnduranceEnd >= $time) {
            // horse is still have endurance
            $distanceCovered = $this->distance($this->bestSpeed(), $time);
        } else {
            // horse finished the endurance
            $distanceCovered = $this->distance($this->bestSpeed(), $timeAtEnduranceEnd) + $this->distance($this->newSpeed(), $time - $timeAtEnduranceEnd);;
        }
        if ($distanceCovered >= race::RACE_DISTANCE) {
            // horse finished the race
            $distanceCovered = race::RACE_DISTANCE;
        }
        $this->distanceCovered = $distanceCovered;
    }

    /**
     * calculate distance
     * @param $speed
     * @param $time
     * @return float
     */
    private function distance($speed, $time)
    {
        return $speed * $time;
    }

    /**
     * generate random number between 0.0 to 10.0
     * @return float|int
     */
    private function generateRandom()
    {
        return rand(0, 100) / 10;
    }

    /**
     * set horse stats
     */
    private function generateHorseStats()
    {
        $speed = $this->generateRandom();
        $strength = $this->generateRandom();
        if ($speed == 0 && $strength == 0) {
            // make sure not to do division by zero
            return $this->generateHorseStats();
        }
        $this->speed = $speed;
        $this->strength = $strength;
        $this->endurance = $this->generateRandom();
    }

    /**
     * check if the horse finished the race
     * @return bool
     */
    public function finishRace(){
        return $this->getDistanceCovered() == race_controller::RACE_DISTANCE;
    }

}