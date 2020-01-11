<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Utilisateur;
use App\Form\Type\UtilisateurType;
use App\Form\Type\ResetPasswordType;
use App\Form\Type\NewPasswordType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;

class UserController extends AbstractController
{
    //Affiche la page d'inscription
    public function signup(Request $request,MailerInterface $mailer){
        
        $utilisateur = new Utilisateur;
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $form->getData();
            $notHashedPassword=$utilisateur->getPassword();
            $utilisateur->setPassword(password_hash($notHashedPassword, PASSWORD_DEFAULT));
            //Génération d'une chaine de caractère aléatoire en hexadecimal pour le token de validation
            $randomString = random_bytes(20);
            $registrationToken=bin2hex($randomString);
            $accountActivationLink="snowtricks/activer-mon-compte/".$registrationToken."";
            $utilisateur->setRegistrationToken($registrationToken);
            $utilisateur->setIsAccountActive(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $this->envoiMailInscription($mailer,$utilisateur,$accountActivationLink);
            return $this->redirectToRoute('app_login');
        }
        return $this->render('signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    public function envoiMailInscription(MailerInterface $mailer,$utilisateur,$accountActivationLink)
    {   
        
        $email = (new Email())
            ->from('clementthuet7@gmail.com')
            ->to('clementthuet7@gmail.com')
            ->subject('Activation de votre compte SnowTricks')
            ->html('<p>Bonjour '.$utilisateur->getPrenom().' '.$utilisateur->getNom().','
                    . '<br>'
                    . 'Cliquez sur le lien ci-dessous pour activer votre compte.'
                    . '<br>'
                    . '<a href='.$accountActivationLink.'>Activer mon compte</a> '
                    . '<br>A bientôt,</p>'
                    .'<p style="margin-left:25%;">L\'équipe SnowTricks</p>');
        $mailer->send($email);
        
        $this->addFlash('successsignup', 'Inscription effectuée avec succès, activez votre compte via le mail qui vous a été envoyé.');
         
    }
    
    public function activerCompte($token)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur= $entityManager->getRepository(Utilisateur::class)->findOneBy(['registrationToken' => $token]);
        if($utilisateur)
        {
            $utilisateur->setIsAccountActive(true);
            $utilisateur->setUrlPhoto('/img/default-user-profile-image.png');
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $this->addFlash('successActivateAccount', 'Le lien sur lequel vous avez cliqué ne correspond à aucun utilisateur, merci de réesayer.');
        }
        else
        {
            $this->addFlash('errorActivateAccount', 'Le lien sur lequel vous avez cliqué ne correspond à aucun utilisateur, merci de réesayer.');
        }
        return $this->redirectToRoute('app_login');
    }
    
    public function motDePasseOublie(Request $request,MailerInterface $mailer)
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $adresseEmail=$form->getData()['email'];
            $entityManager = $this->getDoctrine()->getManager();
            $utilisateur= $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $adresseEmail]);
            if($utilisateur)
            {
                $randomString = random_bytes(20);
                $resetPasswordToken=bin2hex($randomString);
                $utilisateur->setResetToken($resetPasswordToken);
                $entityManager->persist($utilisateur);
                $entityManager->flush();
                $accountResetPasswordLink="snowtricks/reinitialiser-mon-mot-de-passe/".$resetPasswordToken."";
                $email = (new Email())
                ->from('clementthuet7@gmail.com')
                ->to($adresseEmail)
                ->subject('Réinitialisation de votre mot de passe SnowTricks')
                ->html('<p>Bonjour,'
                        . '<br>'
                        . 'Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe.'
                        . '<br>'
                        . '<a href='.$accountResetPasswordLink.'>Réinitialiser votre mot de passe</a> '
                        . '<br>A bientôt,</p>'
                        .'<p style="margin-left:25%;">L\'équipe SnowTricks</p>');
                $mailer->send($email);
                $this->addFlash('successResetPassword', 'Mot de passe réinitialisé avec succès, cliquez sur le lien dans l\'email qui vous a été envoyé pour en définir un nouveau.');
                return $this->redirectToRoute('app_login');
            }
            else
            {
                $this->addFlash('errorResetPasswordMail', 'Cette adresse email ne correspond à aucun compte, veuillez réesayer.');
                return $this->render('motDePasseOublie.html.twig',['form'=>$form->createView()]);
            }
        }
        return $this->render('motDePasseOublie.html.twig',['form'=>$form->createView()]);
    }
    
    public function reinitialiserMotDePasse($resetPasswordToken,Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur= $entityManager->getRepository(Utilisateur::class)->findOneBy(['resetToken' => $resetPasswordToken]);
        if($utilisateur)
        {
            $form = $this->createForm(NewPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
                $entityManager = $this->getDoctrine()->getManager();
                if ($form->getData()['password'] == $form->getData()['passwordCheck'])
                {
                    $rawPassword = $form->getData()['password'];
                    $utilisateur->setPassword(password_hash($rawPassword, PASSWORD_DEFAULT));
                    $utilisateur->setResetToken(NULL);
                    $entityManager->persist($utilisateur);
                    $entityManager->flush();
                    $this->addFlash('successNewPassword', 'Mot de passe modifié avec succès, vous pouvez vous connecter.');
                    return $this->redirectToRoute('app_login');
                }
                else
                {
                    $this->addFlash('errorNotSamePasswords', 'Les mots de passes saisies ne correspondent pas, veuillez réesayer.');
                    return $this->render('definirNouveauMotDePasse.html.twig',['form'=>$form->createView()]);
                }
                
            }
            return $this->render('definirNouveauMotDePasse.html.twig',['form'=>$form->createView()]);
        }
        $this->addFlash('errorResetPasswordToken', 'Le lien sur lequel vous avez cliqué ne correspond pas à votre compte, merci de réesayer.');
        return $this->redirectToRoute('app_login');
    }
    
}

