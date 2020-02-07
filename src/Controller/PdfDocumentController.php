<?php

namespace App\Controller;

use App\Form\PdfUploadType;
use App\Repository\PdfUrlRepository;
use App\Service\PdfRegister;
use App\Service\PdfUploader;
use App\Service\PdfUrlHasher;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var PdfUrlHasher
     */
    private $pdfUrlHasher;
    
    /**
     * PdfDocumentController constructor.
     * @param PdfUrlRepository $repository
     * @param UrlGeneratorInterface $router
     * @param LoggerInterface $logger
     * @param PdfUrlHasher $pdfUrlHasher
     */
    public function __construct(PdfUrlRepository $repository,
                                UrlGeneratorInterface $router,
                                LoggerInterface $logger,
                                PdfUrlHasher $pdfUrlHasher)
    {
        $this->repository = $repository;
        $this->router = $router;
        $this->logger = $logger;
        $this->pdfUrlHasher = $pdfUrlHasher;
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
        if ($pdfUrl) {
            return $this->render("files/index.html.twig", [
                "pdf_url" => $pdfUrl
            ]);
        }
        return $this->redirectToRoute('files.none');
    }
    
    /**
     * @Route("/", name = "files.create", methods={"POST"})
     * @param PdfUploader $pdfUploader
     * @param PdfRegister $pdfRegister
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(PdfUploader $pdfUploader,
                           PdfRegister $pdfRegister,
                           Request $request): Response
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
                $data["token"] = $this->pdfUrlHasher->hashUrl($pdfUrl->getPath());
                return $this->json($data);
            }
        }
        
        $data["error"] = [
            "message" => "aucun fichier généré",
            "form_error" => $form->getErrors()
        ];
        
        
        return $this->json($data);
    }
    
    /**
     * @Route("/{slug}", name="files.delete", methods={"DELETE"})
     * @param $slug
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function delete($slug, EntityManagerInterface $entityManager, Request $request): Response
    {
        $pdfUrl = $this->repository->findOneBy(["path" => $slug]);
        
        $res = [
            "file" => [
                "name" => $slug
            ]
        ];
        
        $token = json_decode($request->getContent())->token ?? null;
        
        try {
            if ($this->pdfUrlHasher->urlHashEquals($slug, $token) && $pdfUrl) {
                
                $entityManager->remove($pdfUrl);
                $entityManager->flush();
                $res["file"]["token_valid"] = "valid token";
                $res["file"]["deleted"] = true;
            } else {
                $res["file"]["deleted"] = false;
                $res["file"]["message"] = 'Invalid token : ' . $token;
            }
        } catch (Exception $ignored) {
            $res["file"]["deleted"] = false;
            $res["file"]["url"] = $pdfUrl->getPath();
            $res["file"]["token"] = $token;
            $res["file"]["slug"] = $this->pdfUrlHasher->hashUrl($slug);
            $res["file"]["message"] = $ignored->getMessage();
        } finally {
            return $this->json($res);
        }
    }
}
