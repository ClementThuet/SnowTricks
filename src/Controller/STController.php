<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\Message;
use App\Form\Type\MessageType;
use App\Repository\MessageRepository;
use App\Repository\FigureRepository;
use App\Form\Type\FigureType;
use App\Form\Type\MediaType;
use App\Form\Type\UpdateFigureType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\UploadMediaFileHelper;
use App\Service\TrickHelper;
use App\Service\MediaHelper;
use App\Service\CommentsHelper;
use App\Service\IndexHelper;

class STController extends AbstractController{
    
    
    public function index(FigureRepository $repository,IndexHelper $indexHelper)
    {
        $resultMoreTricks = $indexHelper->loadFirstTricks($repository);
        return $this->render('index.html.twig',['figures'=>$resultMoreTricks['firstTricks'],'nbTotalTricks'=>$resultMoreTricks['nbTotalTricks']]);
    }
    
    public function afficherFigure(Figure $figure,Request $request,MessageRepository $repository, CommentsHelper $commentsHelper){
        
        $resultCommentHelper = $commentsHelper->getTrickAndFormComment($figure,$repository);
        $message = new Message;
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$this->getUser();
            $commentsHelper->addComment($form,$user,$figure);
            return $this->redirectToRoute('figure',['slug'=>$figure->getSlug()]);
        }
        return $this->render('figure.html.twig',[
            'figure'=>$figure, 'medias'=>$resultCommentHelper['medias'],'messages'=>$resultCommentHelper['firstMessages'],'nbTotalMessages'=>$resultCommentHelper['nbTotalMessages'],'form' => $form->createView()]);
    }
    
    public function afficherPlusFigures(Request $request,FigureRepository $repository,IndexHelper $indexHelper)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $tricks = $indexHelper->loadMoreTricks($request,$repository);
        $response = new Response($tricks);
        return $response;
    }    
    
    public function afficherPlusCommentaires(Request $request,MessageRepository $repository,TrickHelper $trickHelper)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $messages = $trickHelper->loadMoreComments($request,$repository);
        $response = new Response($messages);
        return $response;
    }    
   
    public function ajoutFigure(UploadMediaFileHelper $uploadFileHelper, Request $request)
    {
        //Vérification que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $figure = new Figure();
        $figure->setUtilisateur($user);
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Trick's creation and media upload if it's a picture
            try {
                $uploadFileHelper->uploadMediaFile($figure);
                $this->addFlash('successAddTrick', 'Figure ajoutée avec succès.');
            } catch (\Exception $exception) {
                $this->addFlash('errorAjoutMediaTrick', 'Erreur lors de l\'ajout du média, veuillez réesayer.');
            }

            return $this->redirectToRoute('figure', ['slug' => $figure->getSlug()]);
        }

        return $this->render('ajoutFigureForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    public function modifierFigure($idFigure,TrickHelper $trickHelper,Request $request){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($idFigure);
        $user = $this->getUser();
        //Save olds URLs in case we need them later
        $oldsUrl=$trickHelper->storeOldsUrls($figure);
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setDateDerniereModification(new \DateTime('now'));
            $figure->setDernierUtilisateurModification($user);
            try{
                $trickHelper->uploadMedias($figure,$form,$oldsUrl);
            } catch (Exception $ex) {
                $this->addFlash('errorAjoutMediaTrick', 'Erreur lors de l\'ajout du média, veuillez réesayer.');
                return $this->redirect('/modifier-une-figure/'.$figure->getId());
            }
            $this->addFlash('successEditTrick', 'Figure modifiée avec succès.');
            return $this->redirectToRoute('figure',['slug'=>$figure->getSlug()]);
        }
        return $this->render('modifierFigureForm.html.twig', [
            'form' => $form->createView(),
            'figure'=> $figure
        ]);
    }
    
    public function supprimerFigure($idFigure,TrickHelper $trickHelper){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $trickHelper->deleteTrick($idFigure);
        $this->addFlash('successDeleteTrick', 'Figure supprimée avec succès.');
        return $this->redirectToRoute('index');
    }
    
    
    public function modifierMedia($idMedia,$idFigure,MediaHelper $mediaHelper,Request $request){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $resultFormMediaFigure=$mediaHelper->createFormUpdateMedia($idMedia,$idFigure);
        $form = $this->createForm(MediaType::class, $resultFormMediaFigure['media']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mediaHelper->updateMedia($form,$resultFormMediaFigure['figure']);
            $this->addFlash('successMediaTrick', 'Média modifié avec succès.');
            return $this->redirect('/figure/'.$resultFormMediaFigure['figure']->getSlug().'');
        }
        return $this->render('modifierMediaForm.html.twig', [
            'form' => $form->createView(),
            'media'=>$resultFormMediaFigure['media'],
        ]);
    }
    
    public function supprimerMedia($idMedia,$idFigure, MediaHelper $mediaHelper){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $figure=$mediaHelper->deleteMedia($idMedia,$idFigure);
        $this->addFlash('successDeleteMediaTrick', 'Média supprimé avec succès.');
        return $this->redirect('/figure/'.$figure->getSlug().'');
    }
    
}