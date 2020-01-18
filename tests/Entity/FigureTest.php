<?php

namespace Tests\Entity;

require "config/bootstrap.php";

use PHPUnit\Framework\TestCase;
use App\Entity\Figure;
use App\Entity\Media;

class FigureTest extends TestCase
{
    public function provideStrings()
    {
        return [
            ["A ramdom string",'A ramdom description','A ramdom date']
            ];
    }
    
    public function provideBooleans()
    {
        return [
            [false],
            [true]
            ];
    }
    
    /**
    * @dataProvider provideStrings
    */
    public function testSettingGettingName(string $nom, string $description, string $dateCreation)
    {
        $figure=new Figure();
        $figure->setNom($nom);
        $figure->setDescription($description);
        $figure->setDateCreation($dateCreation);
        $this->assertSame("A ramdom string", $figure->getNom(),'Le nom ne correspond pas');
        $this->assertSame("A ramdom description", $figure->getDescription(),'La description ne correspond pas');
        $this->assertSame("A ramdom date", $figure->getDateCreation(),'La date de création ne correspond pas');
    }
    
    public function testFindMainImage()
    {
        $figure=new Figure();
        $media=new Media();
        $media2=new Media();
        $media->setIsMainPicture(false);
        $media2->setIsMainPicture(true);
        $figure->addMedia($media);
        $figure->addMedia($media2);
        $isMainPicture = $figure->getMainPicture();
        $this->assertIsObject($isMainPicture,"Impossible de trouver l'image principale existante");
    }
    
    public function testFindThereIsNoMainImage()
    {
        $figure=new Figure();
        $media=new Media();
        $media2=new Media();
        $media->setIsMainPicture(false);
        $media2->setIsMainPicture(false);
        $figure->addMedia($media);
        $figure->addMedia($media2);
        $isMainPicture = $figure->getMainPicture();
        $this->assertSame(null,$isMainPicture,"Ne retourne pas qu'il n'existe pas d'image principale");
    }
    
    
    
    /**
    * @dataProvider provideBooleans
    */
   /* public function testGettingMainImage($trueOrFalse)        Another way to test
    {
        $figure=new Figure();
        $media=new Media();
        $media2=new Media();
        if ($trueOrFalse)
        {
            $media->setIsMainPicture(true);
            $media2->setIsMainPicture(false);
            $figure->addMedia($media);
            $figure->addMedia($media2);
            $isMainPicture = $figure->getMainPicture();
            $this->assertIsObject($isMainPicture,"Impossible de trouver l'image principale existante");
        }
        else{
            $media->setIsMainPicture(false);
            $media2->setIsMainPicture(false);
            $figure->addMedia($media);
            $figure->addMedia($media2);
            $isMainPicture = $figure->getMainPicture();
            $this->assertSame(null,$isMainPicture,"Ne retourne pas qu'il n'existe pas d'image principale");
        }
    }*/
    
    
    
}