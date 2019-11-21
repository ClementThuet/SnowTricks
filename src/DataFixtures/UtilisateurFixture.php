<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurFixture extends BaseFixture
{
    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    protected function loadData(ObjectManager $manager)
    {
       $this->createMany(10, 'main_users', function($i) {
            $user = new Utilisateur();
            $user->setEmail(sprintf('SuperUtilisateur%d@gmail.com', $i));
            $user->setNom($this->faker->lastName);
            $user->setPrenom($this->faker->firstName);
            //$user->setRoles(['ROLE_USER']);
            $user->setPassword('test');
            //$user->setMotDePasse($this->passwordEncoder->encodePassword($user,'test'));
            return $user;
        });
        $manager->flush();
    }
}
