<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;

class UploadMediaFileHelper {
    
    public function __construct(ParameterBagInterface $params,EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }
    
    public function uploadMediaFile($user,$form){
        //Récupération des médias pour upload de.s photos
        $entityManager = $this->em;
        $figure = $form->getData();
        $figure->setUtilisateur($user);
        $dateCreation= new \DateTime('now');
        $figure->setDateCreation($dateCreation);
        $medias=$form->getData()->getMedias();
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
                        $this->params->get('images_uploaded_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    //$this->addFlash('successAjoutMediaTrick', 'Erreur lors de l\'ajout du média, veuillez réesayer.'.$e);
                    return $this->redirect('/modifier-une-figure/'.$idFigure.'');
                }
                // updates the property to store the file name
                $media->setUrl('/img/uploads/'.$newFilename);
            }
            $figure->addMedia($media);
        }
        $entityManager->persist($figure);
        $entityManager->flush();
        
        //return $figure;
    }
    
}
