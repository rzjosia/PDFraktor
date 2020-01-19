<?php


namespace App\Service;


use mikehaertl\pdftk\Pdf;
use Spatie\PdfToImage\Exceptions\PdfDoesNotExist;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfHandler
{
    /**
     * @var string
     */
    private $targetDirectory;
    
    /**
     * @var string
     */
    private $separator;
    
    /**
     * @var QrCodeDecoder
     */
    private $qrReader;
    
    /**
     * PdfUploader constructor.
     * @param $targetDirectory
     * @param $separator
     * @param $qrReader
     */
    public function __construct($targetDirectory, $separator, $qrReader)
    {
        $this->targetDirectory = $targetDirectory;
        $this->separator = $separator;
        $this->qrReader = $qrReader;
    }
    
    /**
     * @param UploadedFile $file
     * @return array
     */
    public function parse(UploadedFile $file): array
    {
        $generatedFiles = [];
        
        try {
            $pdf = new \Spatie\PdfToImage\Pdf($file->getRealPath());
            $numberOfPages = $pdf->getNumberOfPages();
            $beginIntercalary = 1;
            
            $intercalaries = $this->qrReader
                ->decode($file->getRealPath())
                ->getIntercalaries($this->separator);
            
            foreach ($intercalaries as $intercalary) {
                $i = $intercalary["index"] + 1;
                if ($i - $beginIntercalary > 0) {
                    $generatedFiles[] = $this->create($file->getRealPath(), $beginIntercalary, $i - 1);
                    $beginIntercalary = $i + 1;
                }
            }
            
            if ($beginIntercalary <= $numberOfPages) {
                $generatedFiles[] = $this->create($file->getRealPath(), $beginIntercalary, $numberOfPages);
            }
            
        } catch (PdfDoesNotExist $e) {
        } finally {
            return $generatedFiles;
        }
        
    }
    
    /**
     * @param String $filePath
     * @param $pageBegin
     * @param $pageEnd
     * @return string
     */
    private
    function create($filePath, $pageBegin, $pageEnd = null): string
    {
        $pdf = new Pdf($filePath);
        $newFileName = $this->generateFileNameWithoutExtension($filePath);
        $filePathName = $this->targetDirectory . '/' . $newFileName . '.pdf';
        $pdf->tempDir = $this->targetDirectory . '/';
        
        if ($pageEnd && $pageEnd > $pageBegin) {
            $pdf
                ->cat($pageBegin, $pageEnd)
                ->saveAs($filePathName);
        } else {
            $pdf
                ->cat($pageBegin)
                ->saveAs($filePathName);
        }
        
        return $newFileName . '.pdf';
    }
    
    /**
     * @param $fileName
     * @return string
     */
    private
    function generateFileNameWithoutExtension($fileName)
    {
        $originalFilename = pathinfo($fileName, PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        return $safeFilename . '-' . uniqid();
    }
}