<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity("email",message="Cette adresse email est déjà utilisée.")
 */
class Utilisateur implements UserInterface
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
    private $password;
    
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    
    /**
     * @see UserInterface
     */
    public function getPassword()
    {
         return $this->password;
    }
    
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }
    
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    
}
