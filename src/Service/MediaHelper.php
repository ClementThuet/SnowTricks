<?php

namespace App\Service;

use App\Entity\Media;
use App\Form\Type\MediaType;
use App\Entity\Figure;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaHelper extends AbstractController
{
    protected $em;
    protected $params;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }

    public function createFormUpdateMedia($idMedia, $idFigure)
    {
        $entityManager = $this->em;
        $figure= $entityManager->getRepository(Figure::class)->find($idFigure);
        $media = $entityManager->getRepository(Media::class)->find($idMedia);
        if($media->getUrl() != null){
            $media->setUrl(new File('C:\wamp64\www\SnowTricks/public'.$media->getUrl()));
        }
        $form = $this->createForm(MediaType::class, $media);
        return ['form'=>$form,'media'=>$media,'figure'=>$figure];
    }
    
    public function updateMedia($form,$figure)
    {
        $entityManager = $this->em;
        $media = $form->getData();
        $figure->setDateDerniereModification(new \DateTime('now'));
        $imageUploaded = $media->getUrl();
        if ($imageUploaded) {
            $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageUploaded->guessExtension();
            $imageUploaded->move(
                $this->params->get('images_uploaded_directory'),
                $newFilename
            );
            // updates the property to store the file name
            $media->setUrl('/img/uploads/'.$newFilename);
        }
        $entityManager->persist($media);
        $entityManager->persist($figure);
        $entityManager->flush();
    }
    public function deleteMedia($idMedia, $idFigure)
    {
        $entityManager = $this->em;
        $figure= $entityManager->getRepository(Figure::class)->find($idFigure);
        $media = $entityManager->getRepository(Media::class)->find($idMedia);
        $entityManager->remove($media);
        $entityManager->flush();
        return $figure;
    }

}
