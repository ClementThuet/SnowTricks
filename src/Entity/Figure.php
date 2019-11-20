<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 * @UniqueEntity("nom",message="Ce nom de figure est déjà utilisé.")
 */
class Figure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message = "Vous devez obligatoirement saisir un nom.")
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Vous devez obligatoirement saisir une description.")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $groupe;
    
    /**
     * @ManyToOne(targetEntity="Utilisateur")
     * @Assert\Type(type="App\Entity\Utilisateur")
     * @Assert\Valid
     */
    private $utilisateur;
    
    /**
     * One figure has many medias. This is the inverse side.
     * @OneToMany(targetEntity="Media", mappedBy="figure", cascade={"persist"})
     */
    private $medias;
    
    public function __construct() {
        $this->medias = new ArrayCollection();
    }
    
    public function getMedias()
    {
        return $this->medias;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(?string $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
    
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    public function setUtilisateur ($utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
    
    public function addMedia(Media $media)
    {
        $media->setFigure($this);
        $this->medias->add($media);
    }

    public function removeMedia(Media $media)
    {
       $this->medias->removeElement($media);
    }
}
