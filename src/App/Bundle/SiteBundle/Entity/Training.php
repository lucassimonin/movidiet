<?php
/**
 * Created by PhpStorm.
 * User: Luk
 * Date: 02/02/2017
 * Time: 11:21
 */

namespace App\Bundle\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Bundle\SiteBundle\Entity\Training
 *
 * @ORM\Table(name="training_patient")
 * @ORM\Entity(repositoryClass="App\Bundle\SiteBundle\Repository\TrainingRepository")
 */
class Training
{
    const MONDAY = 0,
        TUESDAY = 1,
        WENESDAY = 2,
        THURDAY = 3,
        FRIDAY = 4,
        SATURDAY = 5,
        SUNDAY = 6;
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $day
     *
     * @ORM\Column(name="day", type="integer", nullable=false)
     */
    private $day;

    /**
     * @var string $startTime
     *
     * @ORM\Column(name="start_time", type="integer", nullable=false)
     */
    private $startTime;
    /**
     * @var string $endTime
     *
     * @ORM\Column(name="end_time", type="integer", nullable=false)
     */
    private $endTime;

    /**
     * @var string $activity
     *
     * @ORM\Column(name="activity", type="string", length=100, nullable=false)
     */
    private $activity;

    /**
     * @var string $color
     *
     * @ORM\Column(name="color", type="string", length=100, nullable=false)
     */
    private $color;

    /**
     * @var string $userId
     *
     * @ORM\Column(name="user_id", type="string", length=100, nullable=false)
     */
    private $userId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param mixed $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function intToDay()
    {
        $string = '';
        switch($this->day) {
            default:
            case Training::MONDAY:
                $string = 'Lundi';
                break;
            case Training::TUESDAY:
                $string = 'Mardi';
                break;
            case Training::WENESDAY:
                $string = 'Mercredi';
                break;
            case Training::THURDAY:
                $string = 'Jeudi';
                break;
            case Training::FRIDAY:
                $string = 'Vendredi';
                break;
            case Training::SATURDAY:
                $string = 'Samedi';
                break;
            case Training::SUNDAY:
                $string = 'Dimanche';
                break;
        }

        return $string;
    }





}