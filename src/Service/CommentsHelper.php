<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommentsHelper 
{
    protected $em;
    protected $params;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }
    
    public function getTrickAndFormComment($figure,$repository)
    {
        $medias=$figure->getMedias();
        $messages=$figure->getMessages();
        $nbTotalMessages= count($messages);
        //Récupération des X premiers messages (ici 5)
        $firstMessages = $repository->find10Results($figure->getId(),0,5);
        
        return ['nbTotalMessages'=>$nbTotalMessages,'firstMessages'=>$firstMessages,'medias'=>$medias];
    }
    
    public function addComment($form,$user,$figure)
    {
        $entityManager = $this->em;
        $message = $form->getData();
        $message->setUtilisateur($user);
        //$message->setDate(new \DateTime('now'));
        $figure->addMessage($message);
        $entityManager->persist($message);
        $entityManager->persist($figure);
        $entityManager->flush();
    }
    
   

}
