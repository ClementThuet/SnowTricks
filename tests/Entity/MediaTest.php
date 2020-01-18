<?php

namespace Tests\Entity;

require "config/bootstrap.php";

use PHPUnit\Framework\TestCase;
use App\Entity\Media;
use App\Entity\Figure;

class MediaTest extends TestCase
{
   
    public function testTitre()
    {
        $media=new Media();
        $media->setTitre('titre');
        $titre=$media->getTitre();
        $this->assertSame('titre',$titre,"Erreur lors de la récupération du titre");
    }
    
    public function testIsImage()
    {
        $media=new Media();
        $media->setIsImage(true);
        $isImage=$media->getIsImage();
        $this->assertSame(true,$isImage,"Erreur lors de la récupération de isImage");
    }
    
    public function testIsMainPic()
    {
        $media=new Media();
        $media->setIsMainPicture(true);
        $isMainPicture=$media->getIsMainPicture();
        $this->assertSame(true,$isMainPicture,"Erreur lors de la récupération de isMainPicture");
    }
    
    public function testAlt()
    {
        $media=new Media();
        $media->setAlt('description alternative');
        $alt=$media->getAlt();
        $this->assertSame('description alternative',$alt,"Erreur getting alt");
    }
    
    public function testGetTitre()
    {
        $media=new Media();
        $media->setTitre('titre');
        $titre=$media->getTitre();
        $this->assertSame('titre',$titre,"Erreur getting titre");
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