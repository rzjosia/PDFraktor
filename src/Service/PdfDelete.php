<?php


namespace App\Service;


use App\Entity\PdfUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PdfDelete
{
    public function __construct(private readonly string $targetDirectory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function delete(PdfUrl $pdfUrl): void
    {
        $filesystem = new Filesystem();

        foreach ($pdfUrl->getPdfDocuments() as $pdfDocument) {
            $filesystem->remove($this->targetDirectory . "/" . $pdfDocument->getFileName());
        }

        $this->entityManager->remove($pdfUrl);
        $this->entityManager->flush();
    }
}