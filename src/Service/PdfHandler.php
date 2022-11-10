<?php


namespace App\Service;


use Exception;
use mikehaertl\pdftk\Pdf;
use Psr\Log\LoggerInterface;
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
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * PdfUploader constructor.
     * @param $targetDirectory
     * @param $separator
     * @param $qrReader
     * @param LoggerInterface $logger
     */
    public function __construct($targetDirectory, $separator, $qrReader, LoggerInterface $logger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->separator = $separator;
        $this->qrReader = $qrReader;
        $this->logger = $logger;
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

            if ($numberOfPages === 0) {
                throw new Exception('File is empty');
            }

            $this->logger->info('File parsed : ' . $file->getRealPath());
            
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
            $this->logger->error($ignore->getMessage());
        }
        
        return $generatedFiles;
        
    }
    
    /**
     * @param String $filePath
     * @param $pageBegin
     * @param $pageEnd
     * @return string|null
     */
    private function create(string $filePath, $pageBegin, $pageEnd = null): ?string
    {
        $this->logger->info("create file begin");
        
        try {
            $pdf = new Pdf($filePath);
            $newFileName = $this->generateFileNameWithoutExtension($filePath);
            $filePathName = $this->targetDirectory . '/' . $newFileName . '.pdf';
            //$pdf->tempDir = $this->targetDirectory . '/';
    
            if ($pageEnd && $pageEnd > $pageBegin) {
                $res = $pdf
                    ->cat($pageBegin, $pageEnd)
                    ->saveAs($filePathName);
            } else {
                $res = $pdf
                    ->cat($pageBegin)
                    ->saveAs($filePathName);
            }
    
            if (!$res) {
                $this->logger->error("no file created : " . $pdf->getError());
                return null;
            }
    
            $this->logger->info("create file end" . $newFileName . '.pdf');
    
            return $newFileName . '.pdf';
        } catch (Exception $e) {
            $this->logger->error("no file created in " . sys_get_temp_dir() . " => " . $e->getMessage());
        }
        
        return null;
    }
    
    private
    function getNumberOfPages($filePath): int
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
    private
    function generateFileNameWithoutExtension($fileName): string
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
    public
    function getSeparator(): string
    {
        return $this->separator;
    }
}