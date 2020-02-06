<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Utilisateur;

class UserHelper {
    
    
    public function __construct(ParameterBagInterface $params,EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }
    
    public function registerUser($form)
    {
        $utilisateur = $form->getData();
        $notHashedPassword=$utilisateur->getPassword();
        $utilisateur->setPassword(password_hash($notHashedPassword, PASSWORD_DEFAULT));
        //Génération d'une chaine de caractère aléatoire en hexadecimal pour le token de validation
        $randomString = random_bytes(20);
        $registrationToken=bin2hex($randomString);
        $accActivationLink="snowtricks/activer-mon-compte/".$registrationToken."";
        $utilisateur->setRegistrationToken($registrationToken);
        $utilisateur->setIsAccountActive(false);
        $entityManager = $this->em;
        $entityManager->persist($utilisateur);
        $entityManager->flush();
        
        return $accActivationLink;
    }
    
    public function createEmail($utilisateur,$accActivationLink)
    {
        $email = (new Email())
            ->from('clementthuet7@gmail.com')
            ->to('clementthuet7@gmail.com')
            ->subject('Activation de votre compte SnowTricks')
            ->html('<p>Bonjour '.$utilisateur->getPrenom().' '.$utilisateur->getNom().','
                    . '<br>'
                    . 'Cliquez sur le lien ci-dessous pour activer votre compte.'
                    . '<br>'
                    . '<a href='.$accActivationLink.'>Activer mon compte</a> '
                    . '<br>A bientôt.</p>'
                    .'<p style="margin-left:25%;">L\'équipe SnowTricks</p>');
        return $email;
    }
    
    public function checkRegistrationToken($token)
    {
        $entityManager = $this->em;
        $utilisateur= $entityManager->getRepository(Utilisateur::class)->findOneBy(['registrationToken' => $token]);
        $success = false;
        if($utilisateur)
        {
            $utilisateur->setIsAccountActive(true);
            $utilisateur->setUrlPhoto('/img/default-user-profile-image.png');
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $success=true;
        }
        return $success;
    }
    
    public function sendMailResetPassword($form,$mailer)
    {
        $adresseEmail=$form->getData()['email'];
        $entityManager = $this->em;
        $utilisateur= $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $adresseEmail]);
        if($utilisateur)
        {
            $randomString = random_bytes(20);
            $resetPasswordToken=bin2hex($randomString);
            $utilisateur->setResetToken($resetPasswordToken);
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $accResetPWLink="snowtricks/reinitialiser-mon-mot-de-passe/".$resetPasswordToken."";
            $email = (new Email())
            ->from('clementthuet7@gmail.com')
            // Pour la présentation ->to($adresseEmail)
            ->to('clementthuet7@gmail.com')
            ->subject('Réinitialisation de votre mot de passe SnowTricks')
            ->html('<p>Bonjour,'
                    . '<br>'
                    . 'Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe.'
                    . '<br>'
                    . '<a href='.$accResetPWLink.'>Réinitialiser votre mot de passe</a> '
                    . '<br>A bientôt,</p>'
                    .'<p style="margin-left:25%;">L\'équipe SnowTricks</p>');
            $mailer->send($email);
            $success = true;
        }
        return $success;
    }
    
    public function searchUserToReset($resetPasswordToken)
    {
        $entityManager = $this->em;
        $utilisateur= $entityManager->getRepository(Utilisateur::class)->findOneBy(['resetToken' => $resetPasswordToken]);
        return $utilisateur;
    }
    
    public function resetPassword($form,$utilisateur)
    {
        $entityManager = $this->em;
        $successReset = false;
        if ($form->getData()['password'] == $form->getData()['passwordCheck'])
        {
            $rawPassword = $form->getData()['password'];
            $utilisateur->setPassword(password_hash($rawPassword, PASSWORD_DEFAULT));
            $utilisateur->setResetToken(NULL);
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $successReset = true;
        }
        return $successReset;
    }
    
    public function searchUserToDisplay($idUtilisateur)
    {
        $entityManager = $this->em;
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($idUtilisateur);
        return $utilisateur;
    }
    
    public function saveUserChanges($form,$utilisateur)
    {
        $entityManager = $this->em;
        $imageUploaded = $form['urlPhoto']->getData();
        if ($imageUploaded) {
            $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
            try {
                $imageUploaded->move(
                    $this->getParameter('users_pictures'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('successEditUtilisateur', 'Erreur lors de l\'upload de la photo, veuillez réesayer.'.$e);
                return $this->redirect('/profil-membre/'.$idUtilisateur.'');
            }
            $utilisateur->setUrlPhoto('/img/users_pictures/'.$newFilename);
        } 
        $entityManager->persist($utilisateur);
        $entityManager->flush();
    }
}
