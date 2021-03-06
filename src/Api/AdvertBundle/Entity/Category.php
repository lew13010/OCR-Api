<?php

namespace Api\AdvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @ORM\Table(name="Category")
 * @ORM\Entity(repositoryClass="Api\AdvertBundle\Repository\CategoryRepository")
 */
class Category
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
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slugCat", type="string", length=255, unique=true)
     */
    private $slugCat;


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
     * @return category
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
     * Set slugCat
     *
     * @param string $slugCat
     *
     * @return Category
     */
    public function setSlugCat($slugCat)
    {
        $this->slugCat = $slugCat;

        return $this;
    }

    /**
     * Get slugCat
     *
     * @return string
     */
    public function getSlugCat()
    {
        return $this->slugCat;
    }

    public function __toString()
    {
        return $this->name;
    }
}
