<?php

namespace Tests\Entity;

require "config/bootstrap.php";

use PHPUnit\Framework\TestCase;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageTest extends KernelTestCase
{
   /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    
    //Test de si on récupère bien le contenu du premier commentaire en BDD pour une figure donnée
    public function testFindMessages()
    {   
        $message1= new Message();
        $message1->setContenu('First comment');
        $message1->setFigure(24);
        $message2= new Message();
        $message2->setContenu('Second comment');
        $message2->setFigure(24);
        
        $listOfMessages = $this->entityManager
            ->getRepository(Message::class)
            ->find10Results(24,0,2)
        ;
        
        $firstMessage=$listOfMessages[0];
        $this->assertSame('Premier commentaire', $firstMessage->getContenu());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
    
    
    
    
}