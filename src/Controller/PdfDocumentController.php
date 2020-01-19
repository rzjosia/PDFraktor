<?php

namespace App\Controller;

use App\Repository\PdfUrlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * PdfDocumentController constructor.
     * @param PdfUrlRepository $repository
     */
    public function __construct(PdfUrlRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @Route("/", name="files.index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        
        $pdfUrl = $this->repository->findByPath($request->query->get('url'));
        if (!$pdfUrl) {
            throw $this->createNotFoundException("Cette page n'existe pas");
        }
        
        return $this->render("files/index.html.twig", [
            "pdf_url" => $pdfUrl
        ]);
    }
}
