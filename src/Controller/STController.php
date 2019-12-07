<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Utilisateur;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\Message;
use App\Form\Type\MessageType;
use App\Form\Type\UtilisateurType;
use App\Form\Type\UpdateFigureType;
use App\Form\Type\FigureType;
use App\Form\Type\MediaType;
use App\Form\Type\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Common\Collections\ArrayCollection;

class STController extends AbstractController{
    
    
    
    public function index(){
        $entityManager = $this->getDoctrine()->getManager();
        $figures = $entityManager->getRepository(Figure::class)->findAll();
        return $this->render('index.html.twig',['figures'=>$figures]);
    }
    
    public function afficherFigure($nomFigure,Request $request){
        
        $entityManager = $this->getDoctrine()->getManager();
        $figure= $entityManager->getRepository(Figure::class)->findOneBy(['nom' => $nomFigure]);;
        $medias=$figure->getMedias();
        //$messages=$figure->getMessages();
        
        $message = new Message;
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $message->setUtilisateur($this->getUser());
            $message->setDate(new \DateTime('now'));
            $entityManager->persist($message);
            $entityManager->flush();
            return $this->redirectToRoute('figure',['nomFigure'=>$nomFigure]);
        }
        return $this->render('figure.html.twig',['figure'=>$figure, 'medias'=>$medias, 'form' => $form->createView()]);
    }
    
    //Affiche la page d'inscription
    public function signup(Request $request){
        
        $utilisateur = new Utilisateur;
        
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $form->getData();
            $notHashedPassword=$utilisateur->getMotDePasse();
            $utilisateur->setPassword(password_hash($notHashedPassword, PASSWORD_DEFAULT));
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
    
    public function deconnexion(){
        
    }
    
    public function dashboard(){
        //dd($this->getUser());
        return $this->render('dashboard.html.twig');
    }
    
    public function ajoutFigure(Request $request){
        
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        $user = $this->getUser();
   
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $figure = $form->getData();
            $figure->setUtilisateur($user);
            foreach($figure->getMedias() as $media) {
                if(!$figure->getMedias()->contains($media))
                {
                    $figure->addMedia($media);
                }
            }
            $dateCreation= new \DateTime('now');
            $figure->setDateCreation($dateCreation);
            $entityManager->persist($figure);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('ajoutFigureForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    public function modifierFigure($idFigure,Request $request){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($idFigure);
        $user = $this->getUser();
        $medias = new arrayCollection();
        foreach($figure->getMedias() as $media){
            $medias->add($media);
        }
        
        $form = $this->createForm(UpdateFigureType::class, $figure);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setDateDerniereModification(new \DateTime('now'));
            $figure->setDernierUtilisateurModification($user);
            $entityManager->persist($figure);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('modifierFigureForm.html.twig', [
            'form' => $form->createView(),
            'figure'=> $figure,
            'medias'=>$medias
        ]);
    }
    
    public function supprimerFigure($idFigure){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($idFigure);
        $entityManager->remove($figure);
        $entityManager->flush();
        
        return $this->redirectToRoute('index');
    }
    
    public function ajoutMedia($idFigure,Request $request){
        
        // usually you'll want to make sure the user is authenticated first
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

       $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($idFigure);
   
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $media = $form->getData();
            $media->setFigure($figure);
            $figure->addMedia($media);
            
            $entityManager->persist($media);
            $entityManager->persist($figure);
            $entityManager->flush();
            
            return $this->redirect('/SnowTricks/public/modifier-une-figure/'.$idFigure.'');
        }
        return $this->render('ajoutMediaForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    public function ModifierMedia($idMedia,$idFigure,Request $request){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $figure= $entityManager->getRepository(Figure::class)->find($idFigure);
        $media = $entityManager->getRepository(Media::class)->find($idMedia);
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $media = $form->getData();
            $figure->setDateDerniereModification(new \DateTime('now'));
            
            $entityManager->persist($media);
            $entityManager->persist($figure);
            $entityManager->flush();
            
            return $this->redirect('/SnowTricks/public/modifier-une-figure/'.$idFigure.'');
        }
        return $this->render('modifierMediaForm.html.twig', [
            'form' => $form->createView(),
            'media'=>$media,
        ]);
        
        return $this->redirect('/SnowTricks/public/modifier-une-figure/'.$idFigure.'');
    }
    
    public function supprimerMedia($idMedia,$idFigure){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $media = $entityManager->getRepository(Media::class)->find($idMedia);
        $entityManager->remove($media);
        $entityManager->flush();
        
        return $this->redirect('/SnowTricks/public/modifier-une-figure/'.$idFigure.'');
    }
    
}