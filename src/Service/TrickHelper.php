<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Figure;

class TrickHelper {
    
    
    public function __construct(ParameterBagInterface $params,EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }
    
    public function storeOldsUrls($figure)
    {
        $medias=$figure->getMedias();
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
            else{
                array_push($oldsUrl,'C:\wamp64\www\SnowTricks/public/img/default-image.jpg');
                $media->setUrl(new File('C:\wamp64\www\SnowTricks/public/img/default-image.jpg'));
            }
        }
        
        return $oldsUrl;
    }
    
    public function uploadMedias($figure,$form,$oldsUrl)
    {
        $entityManager = $this->em;
        $medias=$form->getData()->getMedias();
        $cpt=0; // Used to found back the previous url
        foreach($medias as $media )
        {
            $imageUploaded = $media->getUrl();
            if ($imageUploaded) {
                $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
                // Move the file to the directory 
                $imageUploaded->move($this->params->get('images_uploaded_directory'),$newFilename);
                // updates the property to store the file name
                $media->setUrl('/img/uploads/'.$newFilename);
            }
            //Use the old url if the form is submit without modification of the url or the url video
            if($media->getUrl() == null && $media->getUrlVideo() == null && defined($oldsUrl[0]) )
            {
                $media->setUrl($oldsUrl[$cpt]);
                $cpt+=1;
            }
            $figure->addMedia($media);
        }
        $entityManager->persist($figure);
        $entityManager->flush();
    }
    
    public function loadMoreComments($request, $repository)
    {
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
        return $messages;
    }
    
    public function deleteTrick($idFigure)
    {
        $entityManager = $this->em;
        $figure = $entityManager->getRepository(Figure::class)->find($idFigure);
        $entityManager->remove($figure);
        $entityManager->flush();
    }
}
