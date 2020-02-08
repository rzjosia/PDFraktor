<?php

namespace App\Controller;

use App\Form\PdfUploadType;
use App\Service\PdfHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    
    /**
     * HomeController constructor.
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $router
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }
    
    /**
     * @Route("/", name="home")
     * @param PdfHandler $pdfHandler
     * @return Response
     */
    public function index(PdfHandler $pdfHandler): Response
    {
        $form = $this->createForm(PdfUploadType::class);
        return $this->render('home/index.html.twig', [
            "form" => $form->createView(),
            "title" => "Découper votre PDF en plusieur morceaux",
            "separator" => $pdfHandler->getSeparator()
        ]);
    }
    
    /**
     * @Route("/mentions-legales", name="mentions.legales")
     * @return Response
     */
    public function legal(): Response
    {
        return $this->render('mention.legal.html.twig', [
            'title' => "Mentions légales"
        ]);
    }
}
