<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Utilisateur;
use App\Entity\Figure;
use App\Form\Type\UtilisateurType;
use App\Form\Type\FigureType;
use App\Form\Type\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class STController extends AbstractController{
    
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    public function index(){
        
        $entityManager = $this->getDoctrine()->getManager();
        $figures = $entityManager->getRepository(Figure::class)->findAll();
        return $this->render('index.html.twig',['figures'=>$figures]);
    }
    
    //Affiche la page d'inscription
    public function signup(Request $request){
        
        $utilisateur = new Utilisateur;
        
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
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
    
    //Affiche la page de login avec l'état de la connexion, par défaut NULL
    public function login(Request $request)
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $data = $form->getData();
            $hashedPassword=$data['motDePasse'];
            $utilisateurs = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
            foreach ($utilisateurs as $utilisateur)
            {
                if(password_verify($hashedPassword, $utilisateur->getMotDePasse()))
                {
                    //Stocker infos en session
                    $this->session->set('logged', true);
                    $this->session->set('utilisateur-id', $utilisateur->getId());
                    $this->session->set('utilisateur-nom', $utilisateur->getNom());
                    $this->session->set('utilisateur-prenom', $utilisateur->getPrenom());
                    $this->session->set('utilisateur-email', $utilisateur->getEmail());
                    return $this->redirectToRoute('dashboard');
                }
            }
            return $this->render('login.html.twig', [
                'form' => $form->createView()]);
        }
        
        return $this->render('login.html.twig', [
            'form' => $form->createView()]);
    }
    
    //Détruit la session
    public function deconnexion(){
        
        $this->session->invalidate();
        return $this->render('index.html.twig');
    }
    
    public function dashboard(){
        return $this->render('dashboard.html.twig');
    }
    
    public function ajoutFigure(Request $request){
        
        $figure = new Figure;
        
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $figure = $form->getData();
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($this->session->get('utilisateur-id'));
            $figure->setUtilisateur($utilisateur);
            $entityManager->persist($figure);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('ajoutFigureForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}