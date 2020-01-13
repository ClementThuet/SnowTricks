<?php

namespace Tests\Entity;

require "config/bootstrap.php";

use PHPUnit\Framework\TestCase;
use App\Entity\Media;
use App\Entity\Figure;

class MediaTest extends TestCase
{
   
    public function testGettingSetting()
    {
        $media=new Media();
        $media->setTitre('titre');
        $media->setIsImage(true);
        $media->setIsMainPicture(true);
        $media->setAlt('description alternative');
        
        $titre=$media->getTitre();
        $isImage=$media->getIsImage();
        $isMainPicture=$media->getIsMainPicture();
        $alt=$media->getAlt();
        
        $this->assertSame('titre',$titre,"Erreur getting titre");
        $this->assertSame(true,$isImage,"Erreur getting isImage");
        $this->assertSame(true,$isMainPicture,"Erreur getting isMainPicture");
        $this->assertSame('description alternative',$alt,"Erreur getting alt");
    }
    
    public function testGettingFigure()
    {
        $figure=new Figure();
        $figure->setNom('Olie');
        $media=new Media();
        $figure->addMedia($media);
        $nomFigure=$media->getFigure()->getNom();
        $this->assertSame('Olie', $nomFigure,'Erreur lors de la récupération de la figure');
    }
    
    public function testSettingFigure()
    {
        $figure=new Figure();
        $figure->setNom('Olie');
        $media=new Media();
        $media->setFigure($figure);
        $figureMedia=$media->getFigure()->getNom();
        $this->assertSame('Olie', $figureMedia,'Erreur lors de l\'affectation de la figure');
    }
    
    
    
    
    
}