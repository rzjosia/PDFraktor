<?php


namespace App\Service;


use Exception;
use mikehaertl\pdftk\Pdf;
use Psr\Log\LoggerInterface;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfHandler
{
    public function __construct(private readonly string $targetDirectory, private readonly string $separator, private readonly QrCodeDecoder $qrCodeDecoder, private readonly LoggerInterface $logger)
    {
    }

    public function parse(UploadedFile $uploadedFile): array
    {
        $generatedFiles = [];
        try {
            $numberOfPages = $this->getNumberOfPages($uploadedFile->getRealPath());

            if ($numberOfPages === 0) {
                throw new Exception('File is empty');
            }

            $this->logger->info('File parsed : ' . $uploadedFile->getRealPath());

            $intercalaries = $this->qrCodeDecoder
                ->decode($uploadedFile->getRealPath())
                ->getIntercalaries($this->separator);

            $sectionBegin = 1;

            foreach ($intercalaries as $intercalary) {
                if ($intercalary["page"] - $sectionBegin > 0) {
                    $generatedFiles[] = $this->create(
                        $uploadedFile->getRealPath(),
                        $sectionBegin,
                        $intercalary['index']);
                }

                $sectionBegin = $intercalary["page"] + 1;
            }

            if ($sectionBegin > 1 && $sectionBegin <= $numberOfPages) {
                $generatedFiles[] = $this->create($uploadedFile->getRealPath(), $sectionBegin, $numberOfPages);
            }

        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $generatedFiles;

    }

    private function create(string $filePath, int|float $pageBegin, $pageEnd = null): ?string
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
        } catch (Exception $exception) {
            $this->logger->error("no file created in " . sys_get_temp_dir() . " => " . $exception->getMessage());
        }
        
        return null;
    }

    private
    function getNumberOfPages(string $filePath): int
    {
        $parser = new Parser();
        try {
            $document = $parser->parseFile($filePath);
            return $document->getDetails()["Pages"];
        } catch (Exception) {
            return 0;
        }
    }

    private function generateFileNameWithoutExtension(string $fileName): string
    {
        $originalFilename = pathinfo($fileName, PATHINFO_FILENAME);

        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename);

        return $safeFilename . '-' . uniqid();
    }
    
    public
    function getSeparator(): string
    {
        return $this->separator;
    }
}