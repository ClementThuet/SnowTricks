<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Figure;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class IndexHelper 
{
    protected $em;
    protected $params;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->params = $params;
        $this->em = $em;
    }
    
    public function loadFirstTricks($repository)
    {
        $entityManager = $this->em;
        $figures = $entityManager->getRepository(Figure::class)->findAll();
        //Récupération des X premiers  (ici 8)
        $firstsTricks = $repository->findTricksFromXToX(0,8);
        $nbTotalTricks= count($figures);
        
        return ['firstTricks'=>$firstsTricks,'nbTotalTricks'=>$nbTotalTricks];
    }
    
    public function loadMoreTricks($request,$repository)
    {
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
        
        return $tricks;
    }
    
    

}
