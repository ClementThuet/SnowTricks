<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"nom"})
     */
    private $slug;
    
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
     * @Assert\Date
     * @ORM\Column(type="date")
     */
    private $dateCreation;
    
    /**
     * @Assert\Date
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateDerniereModification;
    
    
    /**
     * @ManyToOne(targetEntity="Utilisateur")
     * @Assert\Type(type="App\Entity\Utilisateur")
     * @Assert\Valid
     */
    private $utilisateur;
    
    /**
     * @ManyToOne(targetEntity="Utilisateur")
     * @Assert\Type(type="App\Entity\Utilisateur")
     * @Assert\Valid
     */
    private $dernierUtilisateurModification;
    
    /**
     * One figure has many medias. This is the inverse side.
     * @OneToMany(targetEntity="Media", mappedBy="figure", cascade={"persist", "remove"})
     */
    private $medias;
    
    /**
     * One figure has many medias. This is the inverse side.
     * @OneToMany(targetEntity="Message", mappedBy="figure",  cascade={"persist", "remove"})
    */
    private $messages;
    

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }
    
    public function getMedias()
    {
        return $this->medias;
    }
    
    //Permet de savoir si / retrouver l'image principale si il en existe une
    public function getMainPicture()
    {
        $medias=$this->getMedias();
        foreach($medias as $media){
            if ($media->getIsMainPicture() == true){
               return $media;
            }
        }
        return null;
    }
    
    function getSlug() {
        return $this->slug;
    }

    function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getGroupe()
    {
        return $this->groupe;
    }

    public function setGroupe($groupe)
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
    
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
    
    public function getDateDerniereModification()
    {
        return $this->dateDerniereModification;
    }

    public function setDateDerniereModification($dateDerniereModif)
    {
        $this->dateDerniereModification = $dateDerniereModif;

        return $this;
    }
    
    function getDernierUtilisateurModification() {
        return $this->dernierUtilisateurModification;
    }

    function setDernierUtilisateurModification($lastUserUpdate) {
        $this->dernierUtilisateurModification = $lastUserUpdate;
    }
    
    function getMessages() {
        return $this->messages;
    }

    public function addMessage(Message $message)
    {
        $message->setFigure($this);
        $this->messages->add($message);
    }

    public function removeMessage(Message $message)
    {
       $this->messages->removeElement($message);
    }
}
