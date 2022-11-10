<?php


namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfUploader
{
    /**
     * @var PdfHandler
     */
    private $pdfHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PdfUploader constructor.
     * @param $pdfHandler
     * @param LoggerInterface $logger
     */
    public function __construct($pdfHandler, LoggerInterface $logger)
    {
        $this->pdfHandler = $pdfHandler;
        $this->logger = $logger;
    }
    
    /**
     * @param UploadedFile $file
     * @return array
     */
    public function upload(UploadedFile $file): array
    {
        $generatedFiles = [];
        try {
            if (!$file->isFile()) {
                throw new FileException('File is not a found');
            }

            $this->logger->info('File uploaded', ['file' => $file->getClientOriginalName()]);
            $generatedFiles = $this->pdfHandler->parse($file);
        } catch (FileException $e) {
            $this->logger->error($e->getMessage());
        } finally {
            return $generatedFiles;
        }
    }
}