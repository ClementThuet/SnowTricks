<?php

namespace App\Service;

use App\Entity\Media;
use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadMediaFileHelper
{
    protected $em;
    protected $params;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }

    public function uploadMediaFile(Figure $figure)
    {
        /** @var Media $media */
        foreach ($figure->getMedias() as $media) {
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
            $figure->addMedia($media);
        }

        $this->em->persist($figure);
        $this->em->flush();

        return $figure;
    }

}
