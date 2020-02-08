<?php


namespace App\Service;


use Exception;
use mikehaertl\pdftk\Pdf;
use Smalot\PdfParser\Parser;
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
            $numberOfPages = $this->getNumberOfPages($file->getRealPath());
            
            $intercalaries = $this->qrReader
                ->decode($file->getRealPath())
                ->getIntercalaries($this->separator);
            
            $sectionBegin = 1;
            
            foreach ($intercalaries as $intercalary) {
                if ($intercalary["page"] - $sectionBegin > 0) {
                    $generatedFiles[] = $this->create(
                        $file->getRealPath(),
                        $sectionBegin,
                        $intercalary['index']);
                }
                $sectionBegin = $intercalary["page"] + 1;
            }
            
            if ($sectionBegin > 1 && $sectionBegin <= $numberOfPages) {
                $generatedFiles[] = $this->create($file->getRealPath(), $sectionBegin, $numberOfPages);
            }
            
        } catch (Exception $ignore) {
        
        }
        
        return $generatedFiles;
        
    }
    
    /**
     * @param String $filePath
     * @param $pageBegin
     * @param $pageEnd
     * @return string
     */
    private function create($filePath, $pageBegin, $pageEnd = null): string
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
    
    private function getNumberOfPages($filePath): int
    {
        $parser = new Parser();
        try {
            $pdf = $parser->parseFile($filePath);
            return $pdf->getDetails()["Pages"];
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * @param $fileName
     * @return string
     */
    private function generateFileNameWithoutExtension($fileName)
    {
        $originalFilename = pathinfo($fileName, PATHINFO_FILENAME);
        
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename);
        
        return $safeFilename . '-' . uniqid();
    }
    
    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }
}