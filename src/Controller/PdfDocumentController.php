<?php

namespace App\Controller;

use App\Form\PdfUploadType;
use App\Repository\PdfUrlRepository;
use App\Service\PdfDelete;
use App\Service\PdfRegister;
use App\Service\PdfUploader;
use App\Service\PdfUrlHasher;
use Exception;
use JsonException;
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
    public function __construct(private readonly PdfUrlRepository $pdfUrlRepository, private readonly UrlGeneratorInterface $urlGenerator, LoggerInterface $logger, private readonly PdfUrlHasher $pdfUrlHasher)
    {
    }

    /**
     * @Route("/no_content", name="files.none", methods={"GET"})
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
        $pdfUrl = $this->pdfUrlRepository->findOneBy(["path" => $slug]);
        if ($pdfUrl) {
            return $this->render("files/index.html.twig", [
                "pdf_url" => $pdfUrl
            ]);
        }

        return $this->redirectToRoute('files.none');
    }

    /**
     * @Route("/", name = "files.create", methods={"POST"})
     * @throws Exception
     */
    public function create(PdfUploader $pdfUploader,
                           PdfRegister $pdfRegister,
                           Request     $request): Response
    {
        $data = [];
        $form = $this->createForm(PdfUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $generatedFiles = $pdfUploader->upload($form->get("document")->getData());

            // Enregistrer les fichier générer dans la base de donnée s'il y en a
            // Si oui, génér& l'URL
            if ($generatedFiles !== []) {

                $pdfUrl = $pdfRegister->register($generatedFiles);

                $data["url"] = $this->urlGenerator->generate('files.show', ["slug" => $pdfUrl->getPath()]);
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
     * @throws JsonException
     */
    public function delete(string $slug, PdfDelete $pdfDelete, Request $request): Response
    {
        $pdfUrl = $this->pdfUrlRepository->findOneBy(["path" => $slug]);

        $res = [
            "file" => [
                "name" => $slug
            ]
        ];

        $token = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR)->token ?? null;

        try {
            if ($this->pdfUrlHasher->urlHashEquals($slug, $token) && $pdfUrl) {

                $pdfDelete->delete($pdfUrl);

                $res["file"]["token_valid"] = "valid token";
                $res["file"]["deleted"] = true;
            } else {
                $res["file"]["deleted"] = false;
                $res["file"]["message"] = 'Invalid token : ' . $token;
            }
        } catch (Exception $exception) {
            $res["file"]["deleted"] = false;
            $res["file"]["url"] = $pdfUrl->getPath();
            $res["file"]["token"] = $token;
            $res["file"]["slug"] = $this->pdfUrlHasher->hashUrl($slug);
            $res["file"]["message"] = $exception->getMessage();
        } finally {
            return $this->json($res);
        }
    }
}
