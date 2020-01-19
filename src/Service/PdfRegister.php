<?php


namespace App\Service;


use App\Entity\PdfDocument;
use App\Entity\PdfUrl;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PdfRegister
{
    /**
     * @var $string
     */
    private $targetDirectory;
    
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    /**
     * PdfRegister constructor.
     * @param $targetDirectory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($targetDirectory, EntityManagerInterface $entityManager)
    {
        $this->targetDirectory = $targetDirectory;
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param array $generatedFiles
     * @return PdfUrl
     * @throws Exception
     */
    public function register(array $generatedFiles): PdfUrl
    {
        $pdfUrl = new PdfUrl();
        
        $pdfUrl
            ->setPath(uniqid("pdfraktor_"))
            ->setCreatedAt(new \DateTime());
        
        foreach ($generatedFiles as $file) {
            $pdfDocument = new PdfDocument();
            
            $pdfDocument
                ->setFileName($file)
                ->setCreatedAt(new \DateTime());
            
            $pdfUrl->addPdfDocument($pdfDocument);
            
            $this->entityManager->persist($pdfDocument);
        }
        
        $this->entityManager->flush();
        return $pdfUrl;
    }
}