<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\Message;
use App\Form\Type\MessageType;
use App\Repository\MessageRepository;
use App\Form\Type\UpdateFigureType;
use App\Form\Type\MediaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;


class STController extends AbstractController{
    
    
    public function index(){
        $entityManager = $this->getDoctrine()->getManager();
        $figures = $entityManager->getRepository(Figure::class)->findAll();
        return $this->render('index.html.twig',['figures'=>$figures]);
    }
    
    public function afficherFigure($nomFigure,Request $request,MessageRepository $repository){
        
        $entityManager = $this->getDoctrine()->getManager();
        $figure= $entityManager->getRepository(Figure::class)->findOneBy(['nom' => $nomFigure]);
        $medias=$figure->getMedias();
        $messages=$figure->getMessages();
        $nbTotalMessages= count($messages);
        
        //Récupération des X premiers messages (ici 5)
        $FirstsMessages = $repository->find10Results($figure->getId(),0,5);

        //Ajout d'un message à la figure
        $message = new Message;
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $message->setUtilisateur($this->getUser());
            $message->setDate(new \DateTime('now'));
            $figure->addMessage($message);
            $entityManager->persist($message);
            $entityManager->persist($figure);
            $entityManager->flush();
            return $this->redirectToRoute('figure',['nomFigure'=>$nomFigure]);
        }
        return $this->render('figure.html.twig',[
            'figure'=>$figure, 'medias'=>$medias,'messages'=>$FirstsMessages,'nbTotalMessages'=>$nbTotalMessages,'form' => $form->createView()]);
    }
    
    public function afficherPlusCommentaires(Request $request,MessageRepository $repository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $idFigure=$request->request->get('idFigure');
        $firstMessage=$request->request->get('firstMessage');
        $nbMessageToAdd=$request->request->get('nbMessageToAdd');
        // Recherche des $nbMessageToAdd messages suivants
        $TenMoreMessages = $repository->find10Results($idFigure,$firstMessage,$nbMessageToAdd);
        $messages='';
        foreach ($TenMoreMessages as $message){
            $imgAuteurSrc=$message->getUtilisateur()->getUrlPhoto();
            $contenuMessage=$message->getContenu();
            $dateMessage=$message->getDate()->format('d-m-Y');
            $heureMessage=$message->getDate()->format('H:i');
            $prenomAuteurMessage=$message->getUtilisateur()->getPrenom();
            $nomAuteurMessage=$message->getUtilisateur()->getNom();
            $messages.='<div class=\'commentaire\'>'
                    .'<div class="commentaire-photo-container">'
                        .'<img class="photo-user-comment" src="'.$imgAuteurSrc.'" alt="Image représentant '.$prenomAuteurMessage.' '.$nomAuteurMessage.'">'
                    .'</div>'    
                    .'<div class=\'trick-comment\'>
                            <p class="trick-comment-content">'.$contenuMessage.'</p>
                            <span class="trick-comment-date-author"> Le '.$dateMessage.' à '.$heureMessage.' par '.$prenomAuteurMessage.' '.$nomAuteurMessage.'</span>
                        </div>
                    </div>';
        }
        
        $response = new Response($messages);
        return $response;
    }    
    
    public function ajoutFigure(Request $request){
        
        //Vérification que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $user = $this->getUser();
        $figure = new Figure();
        $form = $this->createForm(UpdateFigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $figure = $form->getData();
            $figure->setUtilisateur($user);
            $dateCreation= new \DateTime('now');
            $figure->setDateCreation($dateCreation);
            $entityManager->persist($figure);
            $entityManager->flush();
           // dd($figure->getNom());
            $this->addFlash('successAddTrick', 'Figure ajoutée avec succès.');
            return $this->redirectToRoute('figure',['nomFigure'=>$figure->getNom()]);
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
        //L'erreur se produit ici si les champs du form ssont vides
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setDateDerniereModification(new \DateTime('now'));
            $figure->setDernierUtilisateurModification($user);
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('successEditTrick', 'Figure modifiée avec succès.');
            return $this->redirectToRoute('figure',['nomFigure'=>$figure->getNom()]);
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
        $this->addFlash('successDeleteTrick', 'Figure supprimée avec succès.');
        
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
            
            $imageUploaded = $form['url']->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageUploaded) {
                $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageUploaded->move(
                        $this->getParameter('images_uploaded_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('successAjoutMediaTrick', 'Erreur lors de l\'ajout du média, veuillez réesayer.'.$e);
                    return $this->redirect('/modifier-une-figure/'.$idFigure.'');
                }
                // updates the property to store the file name
                $media->setUrl('/img/uploads/'.$newFilename);
            }
            if($media->getisImage()==false && $form->get('urlVideo')->getData() != null)
            {
                $media->setUrl($form->get('urlVideo')->getData());
            }
            $entityManager->persist($media);
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('successMediaTrick', 'Média ajouté avec succès.');
            return $this->redirect('/modifier-une-figure/'.$idFigure.'');
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
            if($media->getisImage()==false && $form->get('urlVideo')->getData() != null)
            {
                $media->setUrl($form->get('urlVideo')->getData());
            }
            $entityManager->persist($media);
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('successMediaTrick', 'Média modifié avec succès.');
            return $this->redirect('/modifier-une-figure/'.$idFigure.'');
        }
        return $this->render('modifierMediaForm.html.twig', [
            'form' => $form->createView(),
            'media'=>$media,
        ]);
    }
    
    public function supprimerMedia($idMedia,$idFigure){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $this->getDoctrine()->getManager();
        $figure= $entityManager->getRepository(Figure::class)->find($idFigure);
        $media = $entityManager->getRepository(Media::class)->find($idMedia);
        $entityManager->remove($media);
        $entityManager->flush();
        $this->addFlash('successDeleteMediaTrick', 'Média supprimé avec succès.');
        return $this->redirect('/figure/'.$figure->getNom().'');
    }
    
    
    
    
    
}