<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Utilisateur;
use App\Entity\Figure;
use App\Entity\Media;
use App\Form\Type\UtilisateurType;
use App\Form\Type\FigureType;
use App\Form\Type\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class STController extends AbstractController{
    
    
    
    public function index(){
        
        $entityManager = $this->getDoctrine()->getManager();
        $figure= $entityManager->getRepository(Figure::class)->find(2);
       $medias=$figure->getMedias();
         //dd($medias[0]->getTitre());
        $entityManager = $this->getDoctrine()->getManager();
        $figures = $entityManager->getRepository(Figure::class)->findAll();
        return $this->render('index.html.twig',['figures'=>$figures]);
    }
    
    public function afficherFigure($nomFigure){
        
        $entityManager = $this->getDoctrine()->getManager();
        $figure= $entityManager->getRepository(Figure::class)->findOneBy(['nom' => $nomFigure]);;
       
        return $this->render('figure.html.twig',['figure'=>$figure]);
    }
    
    //Affiche la page d'inscription
    public function signup(Request $request){
        
        $utilisateur = new Utilisateur;
        
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $form->getData();
            $notHashedPassword=$utilisateur->getMotDePasse();
            $utilisateur->setMotDePasse(password_hash($notHashedPassword, PASSWORD_DEFAULT));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            return $this->redirectToRoute('Connexion');
        }
        return $this->render('signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    //Affiche la page de login 
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);
        
        return $this->render('login.html.twig', [
                'last_username' => $lastUsername,
                'error'         => $error,
                'form' => $form->createView()]);
    }
    
    public function deconnexion(){
        
    }
    
    public function dashboard(){
        //dd($this->getUser());
        return $this->render('dashboard.html.twig');
    }
    
    public function ajoutFigure(Request $request){
        
         // usually you'll want to make sure the user is authenticated first
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        //$user = $this->getUser();
   
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $figure = $form->getData();
            //EN ATTENDANT DE DEBUGGER LOGIN -> TOUT CREEE PAR USER 1
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find(88);
            $figure->setUtilisateur($utilisateur);
            foreach($figure->getMedias() as $media) {
                if(!$figure->getMedias()->contains($media))
                {
                    $figure->addMedia($media);
                }
            }
            $entityManager->persist($figure);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('ajoutFigureForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}