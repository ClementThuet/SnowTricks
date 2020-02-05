<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UpdateTrickHelper {
    
    private $session;
    
    public function __construct(ParameterBagInterface $params,EntityManagerInterface $em,SessionInterface $session)
    {
        $this->params = $params;
        $this->em = $em;
        $this->session = $session;
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
                try {
                    $imageUploaded->move(
                        $this->getParameter('images_uploaded_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('successAjoutMediaTrick', 'Erreur lors de l\'ajout du média, veuillez réesayer.'.$e);
                    return $this->redirect('/modifier-une-figure/'.$figure->getId());
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
        }
        $entityManager->persist($figure);
        $entityManager->flush();
       $this->session->addFlash('successEditTrick', 'Figure modifiée avec succès.');
    }
}
