<?php

namespace Api\AdvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Api\AdvertBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;


    /**
     * @ORM\ManyToOne(targetEntity="Api\AdvertBundle\Entity\Advert", inversedBy="images")
     */
    private $advert;

    /**
     * @Assert\File(
     *     mimeTypes={"image/png", "image/jpg", "image/jpeg"},
     *     mimeTypesMessage="Ce n'est pas un format autorisé"
     * )
     */
    private $file;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate()
    {
        $this->date = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Image
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Set advert
     *
     * @param \Api\AdvertBundle\Entity\Advert $advert
     *
     * @return Image
     */
    public function setAdvert(\Api\AdvertBundle\Entity\Advert $advert = null)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \Api\AdvertBundle\Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preUpload()
    {

        if(is_null($this->file))
        {
            return;
        }
        $this->image = uniqid().'.'.$this->file->guessExtension();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        if(is_null($this->file))
        {
            return;
        }
        unlink('../web/uploads/'.$this->image);
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate
     */
    public function upload()
    {
        if(is_null($this->file))
        {
            return;
        }
        $this->file->move('../web/uploads/', $this->image);
    }

    /**
     * @ORM\PreRemove
     */
    public function deleteFile()
    {
        if(is_null($this->image))
        {
            return;
        }
        unlink('../web/uploads/'.$this->image);
    }
}
