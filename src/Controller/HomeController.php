<?php

namespace App\Controller;

use App\Form\PdfUploadType;
use App\Service\PdfHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PdfHandler $pdfHandler): Response
    {
        $form = $this->createForm(PdfUploadType::class);
        return $this->render('home/index.html.twig', [
            "form" => $form->createView(),
            "title" => "Découper votre PDF en plusieur morceaux",
            "separator" => $pdfHandler->getSeparator()
        ]);
    }

    #[Route('/mentions-legales', name: 'mentions.legales')]
    public function legal(): Response
    {
        return $this->render('mention.legal.html.twig', [
            'title' => "Mentions légales"
        ]);
    }
}
