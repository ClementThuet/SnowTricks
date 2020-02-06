<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Vous ne pouvez pas poster un commentaire vide.")
     */
    private $contenu;
    
    /**
     * @ManyToOne(targetEntity="Utilisateur")
     * @Assert\Type(type="App\Entity\Utilisateur")
     * @Assert\Valid
     */
    private $utilisateur;
    
    /**
     * @ManyToOne(targetEntity="Figure", inversedBy="messages")
     * @Assert\Type(type="App\Entity\Figure")
     * @Assert\Valid
     */
    private $figure;
   
    public function __construct()
    {
        $this->date = new \DateTime();
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

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
    
    function getFigure() {
        return $this->figure;
    }

    function setFigure($figure) {
        $this->figure = $figure;
    }
}
