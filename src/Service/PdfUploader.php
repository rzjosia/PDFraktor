<?php


namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfUploader
{
    public function __construct(private readonly PdfHandler $pdfHandler, private readonly LoggerInterface $logger)
    {
    }

    public function upload(UploadedFile $uploadedFile): array
    {
        $generatedFiles = [];
        try {
            if (!$uploadedFile->isFile()) {
                throw new FileException('File is not a found');
            }

            $this->logger->info('File uploaded', ['file' => $uploadedFile->getClientOriginalName()]);
            $generatedFiles = $this->pdfHandler->parse($uploadedFile);
        } catch (FileException $fileException) {
            $this->logger->error($fileException->getMessage());
        } finally {
            return $generatedFiles;
        }
    }
}