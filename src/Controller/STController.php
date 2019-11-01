<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class STController extends AbstractController{
    
    public function index(){
        
        return $this->render('index.html.twig');
    }
            
}