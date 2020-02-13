<?php


namespace App\Service;


use App\Entity\PdfUrl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PdfDelete
{
    /**
     * @var string
     */
    private $targetDirectory;
    
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    /**
     * PdfDelete constructor.
     * @param $targetDirectory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($targetDirectory, EntityManagerInterface $entityManager)
    {
        $this->targetDirectory = $targetDirectory;
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param PdfUrl $pdfUrl
     */
    public function delete(PdfUrl $pdfUrl)
    {
        $filesystem = new Filesystem();
        
        foreach ($pdfUrl->getPdfDocuments() as $pdfDocument) {
            $filesystem->remove($this->targetDirectory . "/" . $pdfDocument->getFileName());
        }
        
        $this->entityManager->remove($pdfUrl);
        $this->entityManager->flush();
    }
}