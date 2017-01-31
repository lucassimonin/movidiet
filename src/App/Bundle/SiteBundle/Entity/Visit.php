<?php
/**
 * Created by PhpStorm.
 * User: Luk
 * Date: 30/01/2017
 * Time: 13:27
 */

namespace App\Bundle\SiteBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * App\Bundle\SiteBundle\Entity\Visit
 *
 * @ORM\Table(name="visit_patient")
 * @ORM\Entity
 */
class Visit
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $userId
     *
     * @ORM\Column(name="user_id", type="string", length=100, nullable=false)
     */
    private $userId;

    /**
     * @var date $date
     *
     * @ORM\Column(name="date", type="date", length=100, nullable=true)
     */
    private $date;

    /**
     * @var string $visitJson
     *
     * @ORM\Column(name="visit_json", type="text", nullable=false)
     */
    private $visitJson;

    private $weight;
    private $fatMass;
    private $arm;
    private $thigh;
    private $chest;
    private $hip;
    private $size;


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
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getVisitJson()
    {
        return $this->visitJson;
    }

    /**
     * @param string $visitJson
     */
    public function setVisitJson($visitJson)
    {
        $this->visitJson = $visitJson;
    }
    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }/**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }/**
     * @return mixed
     */
    public function getFatMass()
    {
        return $this->fatMass;
    }/**
     * @param mixed $fatMass
     */
    public function setFatMass($fatMass)
    {
        $this->fatMass = $fatMass;
    }/**
     * @return mixed
     */
    public function getArm()
    {
        return $this->arm;
    }/**
     * @param mixed $arm
     */
    public function setArm($arm)
    {
        $this->arm = $arm;
    }/**
     * @return mixed
     */
    public function getThigh()
    {
        return $this->thigh;
    }/**
     * @param mixed $thigh
     */
    public function setThigh($thigh)
    {
        $this->thigh = $thigh;
    }/**
     * @return mixed
     */
    public function getChest()
    {
        return $this->chest;
    }/**
     * @param mixed $chest
     */
    public function setChest($chest)
    {
        $this->chest = $chest;
    }/**
     * @return mixed
     */
    public function getHip()
    {
        return $this->hip;
    }/**
     * @param mixed $hip
     */
    public function setHip($hip)
    {
        $this->hip = $hip;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    public function _toJson()
    {
        return json_encode(array(
            'weight' => $this->weight,
            'fatMass' => $this->fatMass,
            'arm' => $this->arm,
            'thigh' => $this->thigh,
            'chest' => $this->chest,
            'hip' => $this->hip,
            'size' => $this->size,
        ));
    }

    public function _toArray()
    {
        return json_decode(base64_decode($this->visitJson), true);
    }


}