<?php

namespace App\Controller;

use App\Form\PdfUploadType;
use App\Service\PdfRegister;
use App\Service\PdfUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * HomeController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param PdfUploader $pdfUploader
     * @param PdfRegister $pdfRegister
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, PdfUploader $pdfUploader, PdfRegister $pdfRegister)
    {
        $form = $this->createForm(PdfUploadType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $generatedFiles = $pdfUploader->upload($form->get("document")->getData());
            
            // Enregistrer les fichier générer dans la base de donnée s'il y en a
            // Si oui, génér& l'URL
            if (count($generatedFiles) > 0) {
                $pdfUrl = $pdfRegister->register($generatedFiles);
                return $this->redirect($this->generateUrl('files.index', ["url" => $pdfUrl->getPath()]));
            }
            
            return $this->redirect($this->generateUrl('files.index'));
        }
        
        return $this->render('home/index.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
