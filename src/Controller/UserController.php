<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Utilisateur;
use App\Form\Type\UtilisateurType;
use App\Form\Type\EditUtilisateurType;
use App\Form\Type\ResetPasswordType;
use App\Form\Type\NewPasswordType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\UserHelper;

class UserController extends AbstractController
{
    //Affiche la page d'inscription
    public function signup(Request $request,MailerInterface $mailer,UserHelper $userHelper){
        
        $utilisateur = new Utilisateur;
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $accActivationLink = $userHelper->registerUser($form);
            $this->envoiMailInscription($mailer,$utilisateur,$accActivationLink);
            return $this->redirectToRoute('app_login');
        }
        return $this->render('signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    public function envoiMailInscription(MailerInterface $mailer,$utilisateur,$accActivationLink, UserHelper $userHelper)
    {   
        $email = $userHelper->createEmail($utilisateur,$accActivationLink);
        $mailer->send($email);
        $this->addFlash('successsignup', 'Inscription effectuée avec succès, activez votre compte via le mail qui vous a été envoyé.');
    }
    
    public function activerCompte($token,UserHelper $userHelper)
    {
        $success = $userHelper->checkRegistrationToken($token);
        if ($success){
            $this->addFlash('successActivateAccount', 'Votre compte est activé, vous pouvez désormais vous connecter.');
        }
        else{
            $this->addFlash('errorActivateAccount', 'Le lien sur lequel vous avez cliqué ne correspond à aucun utilisateur, merci de réesayer.');
        }
        return $this->redirectToRoute('app_login');
    }
    
    public function motDePasseOublie(Request $request,MailerInterface $mailer,UserHelper $userHelper)
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $success = $userHelper->sendMailResetPassword($form,$mailer);
            if ($success){
                $this->addFlash('successResetPassword', 'Mot de passe réinitialisé avec succès, cliquez sur le lien dans l\'email qui vous a été envoyé pour en définir un nouveau.');
                return $this->redirectToRoute('app_login');
            }
            else{
                $this->addFlash('errorResetPasswordMail', 'Cette adresse email ne correspond à aucun compte, veuillez réesayer.');
                return $this->render('motDePasseOublie.html.twig',['form'=>$form->createView()]);
            }
        }
        return $this->render('motDePasseOublie.html.twig',['form'=>$form->createView()]);
    }
    
    public function reinitialiserMotDePasse($resetPasswordToken,Request $request,UserHelper $userHelper){
        $utilisateur = $userHelper->searchUserToReset($resetPasswordToken);
        if($utilisateur)
        {
            $form = $this->createForm(NewPasswordType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $successReset = $userHelper->resetPassword($form,$utilisateur);
                if ($successReset){
                    $this->addFlash('successNewPassword', 'Mot de passe modifié avec succès, vous pouvez vous connecter.');
                    return $this->redirectToRoute('app_login');
                }
                else{
                    $this->addFlash('errorNotSamePasswords', 'Les mots de passes saisies ne correspondent pas, veuillez réesayer.');
                    return $this->render('definirNouveauMotDePasse.html.twig',['form'=>$form->createView()]);
                }
            }
            return $this->render('definirNouveauMotDePasse.html.twig',['form'=>$form->createView()]);
        }
        $this->addFlash('errorResetPasswordToken', 'Le lien sur lequel vous avez cliqué ne correspond pas à votre compte, merci de réesayer.');
        return $this->redirectToRoute('app_login');
    }
    
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    
    public function deconnexion()
    {
    }
    
    public function afficherProfilMembre($idUtilisateur, Request $request,UserHelper $userHelper){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $utilisateur = $userHelper->searchUserToDisplay($idUtilisateur);
        $form = $this->createForm(EditUtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userHelper->saveUserChanges($form,$utilisateur);
            $this->addFlash('successEditUtilisateur', 'Profil utilisateur mise à jour avec succès.');
            return $this->redirect('/profil-membre/'.$idUtilisateur.'');
        }
        return $this->render('profilMembre.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur
        ]);
    }
}

