<?php

namespace App\Controller;

use App\Entity\PdfUpload;
use App\Form\PdfUploadType;
use App\Repository\PdfUrlRepository;
use App\Service\PdfRegister;
use App\Service\PdfUploader;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PdfDocumentController
 * @package App\Controller
 * @Route("/files")
 */
class PdfDocumentController extends AbstractController
{
    /**
     * @var PdfUrlRepository
     */
    private $repository;
    
    /**
     * @var UrlGeneratorInterface
     */
    
    private $router;
    /**
     * @var LoggerInterface
     */
    
    private $logger;
    
    /**
     * PdfDocumentController constructor.
     * @param PdfUrlRepository $repository
     * @param UrlGeneratorInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(PdfUrlRepository $repository, UrlGeneratorInterface $router, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->router = $router;
        $this->logger = $logger;
    }
    
    /**
     * @Route("/no_content", name="files.none", methods={"GET"})
     * @return Response
     */
    public function noContent(): Response
    {
        return $this->render("files/index.html.twig");
    }
    
    /**
     * @Route("/{slug}", name="files.show", methods={"GET"})
     * @param $slug
     * @return Response
     */
    public function show($slug): Response
    {
        $pdfUrl = $this->repository->findOneBy(["path" => $slug]);
        return $this->render("files/index.html.twig", [
            "pdf_url" => $pdfUrl
        ]);
    }
    
    /**
     * @Route("/", name = "files.create", methods={"POST"})
     * @param PdfUploader $pdfUploader
     * @param PdfRegister $pdfRegister
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(PdfUploader $pdfUploader, PdfRegister $pdfRegister, Request $request): Response
    {
        $form = $this->createForm(PdfUploadType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $generatedFiles = $pdfUploader->upload($form->get("document")->getData());
            
            // Enregistrer les fichier générer dans la base de donnée s'il y en a
            // Si oui, génér& l'URL
            if (count($generatedFiles) > 0) {
                
                $pdfUrl = $pdfRegister->register($generatedFiles);
                
                $data["url"] = $this->router->generate('files.show', ["slug" => $pdfUrl->getPath()]);
                $data["count_files"] = $pdfUrl->getPdfDocuments()->count();
                return $this->json($data);
            }
        }
        
        $data["error"] = [
            "message" => "aucun fichier généré",
            "form_error" => $form->getErrors()
        ];
        
        
        return $this->json($data);
    }
}
