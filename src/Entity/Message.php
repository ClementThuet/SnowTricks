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
     * @ORM\Column(type="date")
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContenu(): ?string
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
    
}
