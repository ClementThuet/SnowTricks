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
use App\Form\Type\UpdateFigureType;
use App\Form\Type\MediaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\UploadMediaFileHelper;
use App\Service\UpdateTrickHelper;

class STController extends AbstractController{
    
    
    public function index(FigureRepository $repository){
        $entityManager = $this->getDoctrine()->getManager();
        $figures = $entityManager->getRepository(Figure::class)->findAll();
        //Récupération des X premiers  (ici 8)
        $FirstsTricks = $repository->findTricksFromXToX(0,8);
        $nbTotalTricks= count($figures);
        return $this->render('index.html.twig',['figures'=>$FirstsTricks,'nbTotalTricks'=>$nbTotalTricks]);
    }
    
    /*/**
     * @Route("/figure/{slug}", name="figure")
     */
   //
    public function afficherFigure(Figure $figure,Request $request,MessageRepository $repository){
        
        $entityManager = $this->getDoctrine()->getManager();
        //$figure= $entityManager->getRepository(Figure::class)->findOneBy(['slug' => $slug]);
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
            return $this->redirectToRoute('figure',['slug'=>$figure->getSlug()]);
        }
        return $this->render('figure.html.twig',[
            'figure'=>$figure, 'medias'=>$medias,'messages'=>$FirstsMessages,'nbTotalMessages'=>$nbTotalMessages,'form' => $form->createView()]);
    }
    
    public function afficherPlusFigures(Request $request,FigureRepository $repository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $firstTrick=$request->request->get('firstTrick');
        $nbTricksToAdd=$request->request->get('nbTricksToAdd');
        // Recherche des $nbTricksToAdd messages suivants
        $TricksToAdd = $repository->findTricksFromXToX($firstTrick,$nbTricksToAdd);
        $tricks='';
        foreach ($TricksToAdd as $trick){
            $trickId=$trick->getId();
            $trickSlug=$trick->getSlug();
            $trickName=$trick->getNom();
            if ($trick->getMainPicture() != null &&  $trick->getMainPicture()->getIsMainPicture())
            {
                $imageUrl=$trick->getMainPicture()->getUrl();
                $imageAlt=$trick->getMainPicture()->getAlt();
            }
            else{
                $imageUrl="/img/default-image.jpg";
                $imageAlt="Image par défaut";
            }
            
            $tricks.='<div class="col-lg-3 col-md-4 col-sm-12 figure">'
                        .'<div class="media-trick-container">'
                            .'<a href="figure/'.$trickSlug.'">'
                                .'<img class="img-trick" src="'.$imageUrl.'" alt="'.$imageAlt.'"  >'
                            .'</a>'
                            .'<div class="glyph-container">'
                                .'<a href="/modifier-une-figure/'.$trickId.'" '
                                    .'<i class="fas fa-edit glyphicon-edit-hp"></i>'
                                .'</a>'
                                .'<span class="trick-title">'.$trickName.'</span>'
                                .'<a>'
                                    .'<i onclick="deleteTrick('.$trickId.')" class="fa fa-trash glyphicon-remove-hp delete-trick"></i>'
                                .'</a>'
                            .'</div>'
                        .'</div>'
                    .'</div>'
                    ;
            }                
        
        $response = new Response($tricks);
        return $response;
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
   
    public function ajoutFigure(UploadMediaFileHelper $uploadFileHelper, Request $request){
        
        //Vérification que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $user = $this->getUser();
        $figure = new Figure();
        
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Trick's creation and media upload if it's a picture
            $uploadFileHelper->uploadMediaFile($user,$form);
            $this->addFlash('successAddTrick', 'Figure ajoutée avec succès.');
            return $this->redirectToRoute('figure',['slug'=>$figure->getSlug()]);
        }
        return $this->render('ajoutFigureForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    public function modifierFigure($idFigure,UpdateTrickHelper $updateTrickHelper,Request $request){
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($idFigure);
        $user = $this->getUser();
        $updateTrickHelper->storeOldsUrls($figure);
        
        /*$medias=$figure->getMedias();
        // Store the ancients URL to use in case form uis submitted without selected file
        $oldsUrl=[];
        foreach($medias as $media )
        {   
            if($media->getUrl() != null){
                //If the media have an url we store it
                array_push($oldsUrl,$media->getUrl());
                //Allow to generate a file for the FileType with the URL
                $media->setUrl(new File('C:\wamp64\www\SnowTricks/public'.$media->getUrl()));
            }
        }*/
        
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $figure->setDateDerniereModification(new \DateTime('now'));
            $figure->setDernierUtilisateurModification($user);
            $updateTrickHelper->uploadMedias($form);
            //Récupération des médias pour upload de.s photos
            /*$medias=$form->getData()->getMedias();
            $cpt=0; // Used to found back the previous url
            foreach($medias as $media )
            {
                $imageUploaded = $media->getUrl();
                if ($imageUploaded) {
                    $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
                    // Move the file to the directory 
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
                //Use the old url if the form is submit without modification of the url or the url video
                if($media->getUrl() == null AND $media->getUrlVideo() == null)
                {
                    $media->setUrl($oldsUrl[$cpt]);
                    $cpt+=1;
                }
                $figure->addMedia($media);
                
            }*/
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('successEditTrick', 'Figure modifiée avec succès.');
            return $this->redirectToRoute('figure',['slug'=>$figure->getSlug()]);
        }
        return $this->render('modifierFigureForm.html.twig', [
            'form' => $form->createView(),
            'figure'=> $figure
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
    
    //To delete if collections works
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
        $media->setUrl(new File('C:\wamp64\www\SnowTricks/public'.$media->getUrl()));
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
        return $this->redirect('/figure/'.$figure->getSlug().'');
    }
    
    
    
    
    
}