<?php

namespace Api\AdvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UniqueEntity;

/**
 * City
 *
 * @ORM\Table(name="City")
 * @ORM\Entity(repositoryClass="Api\AdvertBundle\Repository\CityRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="La ville est déjà enregistrer"
 * )
 */
class City
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set advert
     *
     * @param \Api\AdvertBundle\Entity\City $advert
     *
     * @return City
     */
    public function setAdvert(\Api\AdvertBundle\Entity\City $advert = null)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \Api\AdvertBundle\Entity\City
     */
    public function getAdvert()
    {
        return $this->advert;
    }
}
