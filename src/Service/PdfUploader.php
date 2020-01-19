<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfUploader
{
    /**
     * @var PdfHandler
     */
    private $pdfHandler;
    
    /**
     * PdfUploader constructor.
     * @param $pdfHandler
     */
    public function __construct($pdfHandler)
    {
        $this->pdfHandler = $pdfHandler;
    }
    
    /**
     * @param UploadedFile $file
     * @return array
     */
    public function upload(UploadedFile $file): array
    {
        $generatedFiles = [];
        try {
            $generatedFiles = $this->pdfHandler->parse($file);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        } finally {
            return $generatedFiles;
        }
    }
}