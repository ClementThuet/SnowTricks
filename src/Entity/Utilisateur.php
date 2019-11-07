<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity("email",message="Cette adresse email est déjà utilisée.")
 */
class Utilisateur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\NotBlank(message = "Vous devez obligatoirement saisir un nom.")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=35, nullable=true)
     */
    private $prenom;

    /**
     * @Assert\NotBlank(message = "Vous devez obligatoirement saisir une adresse email.")
     * @Assert\Email(message = "L'adresse '{{ value }}' n'est pas une adresse email valide.")
     * @ORM\Column(type="string", length=320, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank(message = "Vous devez obligatoirement saisir un mot de passe.")
     * @ORM\Column(type="string", length=200)
     */
    private $motDePasse;
    
    
    
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }
}
