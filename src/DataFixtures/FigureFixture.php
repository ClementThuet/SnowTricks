<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Utilisateur;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FigureFixture extends BaseFixture
{
   private $passwordEncoder;
     
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    protected function loadData(ObjectManager $manager)
    {
       /*$this->createMany(10, 'main_users', function($i) {
            $user = new Utilisateur();
            $user->setEmail(sprintf('SuperUtilisateur%d@gmail.com', $i));
            $user->setNom($this->faker->lastName);
            $user->setPrenom($this->faker->firstName);
            $user->setPassword($this->passwordEncoder->encodePassword($user,'test'));
            return $user;
        });*/
        
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Thuet');
        $utilisateur->setPrenom('Clément');
        $utilisateur->setEmail('clementthuet7@gmail.com');
        $utilisateur->setPassword($this->passwordEncoder->encodePassword($utilisateur,'adminpassword'));
        $utilisateur->setIsAccountActive(true);
        $utilisateur->setUrlPhoto('/img/default-user-profile-image.png');
        
        $noseGrab=new Figure();
        $noseGrab->setNom('Nose grab');
        $noseGrab->setDescription('Saisie de la partie avant de la planche avec la main avant.');
        $noseGrab->setGroupe('Grabs');
        $noseGrab->setDateCreation(new \DateTime('now'));
        $noseGrab->setDateDerniereModification(new \DateTime('now'));
        $noseGrab->setUtilisateur($utilisateur);
        
        $truckDriver=new Figure();
        $truckDriver->setNom('Truck driver');
        $truckDriver->setDescription('Saisie du carre avant et carre arrière avec chaque main (comme pour tenir un volant de voiture).');
        $truckDriver->setGroupe('Grabs');
        $truckDriver->setDateCreation(new \DateTime('now'));
        $truckDriver->setDateDerniereModification(new \DateTime('now'));
        $truckDriver->setUtilisateur($utilisateur);
        
        $mute=new Figure();
        $mute->setNom('Mute');
        $mute->setDescription('Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.');
        $mute->setGroupe('Grabs');
        $mute->setDateCreation(new \DateTime('now'));
        $mute->setDateDerniereModification(new \DateTime('now'));
        $mute->setUtilisateur($utilisateur);
        
        $tailGrab=new Figure();
        $tailGrab->setNom('Tail grab');
        $tailGrab->setDescription('Saisie de la partie arrière de la planche avec la main arrière.');
        $tailGrab->setGroupe('Grabs');
        $tailGrab->setDateCreation(new \DateTime('now'));
        $tailGrab->setDateDerniereModification(new \DateTime('now'));
        $tailGrab->setUtilisateur($utilisateur);
        
        $troisSix=new Figure();
        $troisSix->setNom('360');
        $troisSix->setDescription('Effectuer un tour complet sur soi même.');
        $troisSix->setGroupe('Rotations');
        $troisSix->setDateCreation(new \DateTime('now'));
        $troisSix->setDateDerniereModification(new \DateTime('now'));
        $troisSix->setUtilisateur($utilisateur);
        
        $septDeux=new Figure();
        $septDeux->setNom('720');
        $septDeux->setDescription('Effectuer deux tours complets sur soi même.');
        $septDeux->setGroupe('Rotation');
        $septDeux->setDateCreation(new \DateTime('now'));
        $septDeux->setDateDerniereModification(new \DateTime('now'));
        $septDeux->setUtilisateur($utilisateur);
        
        $frontFlip=new Figure();
        $frontFlip->setNom('Front flip');
        $frontFlip->setDescription('Rotation verticale vers l\'avant.');
        $frontFlip->setGroupe('Flips');
        $frontFlip->setDateCreation(new \DateTime('now'));
        $frontFlip->setDateDerniereModification(new \DateTime('now'));
        $frontFlip->setUtilisateur($utilisateur);
        
        $backFlip=new Figure();
        $backFlip->setNom('Back flip');
        $backFlip->setDescription('Rotation verticale vers l\'arrière.');
        $backFlip->setGroupe('Flips');
        $backFlip->setDateCreation(new \DateTime('now'));
        $backFlip->setDateDerniereModification(new \DateTime('now'));
        $backFlip->setUtilisateur($utilisateur);
        
        $noseSlide=new Figure();
        $noseSlide->setNom('Nose slide');
        $noseSlide->setDescription('Consiste à glisser sur une barre de slide avec l\'avant de la planche sur la barre.');
        $noseSlide->setGroupe('Slides');
        $noseSlide->setDateCreation(new \DateTime('now'));
        $noseSlide->setDateDerniereModification(new \DateTime('now'));
        $noseSlide->setUtilisateur($utilisateur);

        $tailSlide=new Figure();
        $tailSlide->setNom('Tail slide');
        $tailSlide->setDescription('Consiste à glisser sur une barre de slide avec l\'arrière de la planche sur la barre.');
        $tailSlide->setGroupe('Slides');
        $tailSlide->setDateCreation(new \DateTime('now'));
        $tailSlide->setDateDerniereModification(new \DateTime('now'));
        $tailSlide->setUtilisateur($utilisateur);
        
        $manager->persist($utilisateur);
        $manager->persist($truckDriver);
        $manager->persist($mute);
        $manager->persist($tailGrab);
        $manager->persist($noseGrab);
        $manager->persist($troisSix);
        $manager->persist($septDeux);
        $manager->persist($frontFlip);
        $manager->persist($backFlip);
        $manager->persist($noseSlide);
        $manager->persist($tailSlide);
        $manager->flush();
    }
}
