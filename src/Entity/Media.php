<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank(message = "Vous devez obligatoirement saisir un nom.")
    */
    private $titre;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $isImage;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isMainPicture;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;
    
    /**
     * @ManyToOne(targetEntity="Figure", inversedBy="medias")
     * @Assert\Type(type="App\Entity\Figure")
     * @Assert\Valid
     */
    private $figure;
    
    
    public function getFigure()
    {
        return $this->figure;
    }

    public function setFigure($figure)
    {
        $this->figure = $figure;

        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre( $titre)
    {
        $this->titre = $titre;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }
    
    public function getIsImage()
    {
        return $this->isImage;
    }

    public function setIsImage($isImage)
    {
        $this->isImage = $isImage;

        return $this;
    }

    public function getIsMainPicture()
    {
        return $this->isMainPicture;
    }

    public function setIsMainPicture($isMainPicture)
    {
        $this->isMainPicture = $isMainPicture;

        return $this;
    }
    
   
}
