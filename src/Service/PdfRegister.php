<?php


namespace App\Service;


use App\Entity\PdfDocument;
use App\Entity\PdfUrl;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PdfRegister
{
    public function __construct($targetDirectory, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    public function register(array $generatedFiles): PdfUrl
    {
        $pdfUrl = new PdfUrl();

        $pdfUrl
            ->setPath(uniqid("pdfraktor_"))
            ->setCreatedAt(new DateTime());

        foreach ($generatedFiles as $generatedFile) {
            $pdfDocument = new PdfDocument();

            $pdfDocument
                ->setFileName($generatedFile)
                ->setCreatedAt(new DateTime());

            $pdfUrl->addPdfDocument($pdfDocument);

            $this->entityManager->persist($pdfDocument);
        }

        $this->entityManager->flush();
        return $pdfUrl;
    }
}