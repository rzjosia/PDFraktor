<?php

namespace App\Tests;

use App\Service\PdfHandler;
use App\Service\QrCodeDecoder;
use Qipsius\TCPDFBundle\Controller\TCPDFController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use TCPDF;

/**
 * @property PdfHandler|object|null pdfHandler
 * @property QrCodeDecoder|object|null QrCodeDecoder
 * @property object|TCPDFController|null tcpdf
 * @property string testDirectory
 */
class SplitTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;
        $this->QrCodeDecoder = $container->get("App\Service\QrCodeDecoder");
        $this->pdfHandler = $container->get("App\Service\PdfHandler");
        $this->tcpdf = $container->get("qipsius.tcpdf");
        $this->testDirectory = $this->pdfHandler->getTargetDirectory() . "/test";
        
        $filesytem = new Filesystem();
        $filesytem->mkdir($this->testDirectory);
    }
    
    protected function tearDown()
    {
        $filesytem = new Filesystem();
        $filesytem->remove($this->testDirectory);
    }
    
    public function testQrCodeDecoderWithRightSeparator()
    {
        $filename = $this->testDirectory . '/testQrCodeDecoderWithRightSeparator.pdf';
        
        $intercalaries = [
            [
                "page" => 4,
                "qrcode" => $this->pdfHandler->getSeparator()
            ],
            [
                "page" => 6,
                "qrcode" => $this->pdfHandler->getSeparator()
            ],
            [
                "page" => 8,
                "qrcode" => $this->pdfHandler->getSeparator()
            ],
        ];
        
        $pdf = $this->createPDF(10, $intercalaries);
        $pdf->Output($filename, 'F');
        self::assertFileExists($filename);
        
        $output = $this->QrCodeDecoder
            ->decode($filename)
            ->getIntercalaries($this->pdfHandler->getSeparator());
        
        self::assertIsArray($output);
        self::assertTrue(count($intercalaries) == count($output));
    }
    
    public function testQrCodeDecoderWithWrongSeparator()
    {
        $filename = $this->testDirectory . '/testQrCodeDecoderWithWrongSeparator.pdf';
        
        $intercalaries = [
            [
                "page" => 4,
                "qrcode" => "wrong intercalary"
            ],
            [
                "page" => 6,
                "qrcode" => $this->pdfHandler->getSeparator()
            ],
            [
                "page" => 8,
                "qrcode" => "wrong separator"
            ],
        ];
        
        $pdf = $this->createPDF(10, $intercalaries);
        $pdf->Output($filename, 'F');
        self::assertFileExists($filename);
        
        $output = $this->QrCodeDecoder
            ->decode($filename)
            ->getIntercalaries($this->pdfHandler->getSeparator());
        
        self::assertIsArray($output);
        self::assertFalse(count($intercalaries) == count($output));
        self::assertCount(1, $output, "Only one separator. Must be equal to 1");
    }
    
    
    /**
     * @param int $pageNumber
     * @param array $intercalaries
     * @return TCPDF
     */
    public function createPDF($pageNumber, array $intercalaries): TCPDF
    {
        try {
            $pdf = $this->tcpdf->create();
            
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Josia RAZAFINJATOVO');
            $pdf->SetTitle('Test QrCode PDFraktor');
            $pdf->SetSubject('PDFraktor test');
            $pdf->SetKeywords('TCPDF, PDF, PDFraktor, test');
            
            $intercalariesIndex = 0;
            $pageNumberIndex = 0;
            $fragment = 1;
            
            while ($pageNumberIndex < $pageNumber) {
                $pdf->AddPage();
                
                if ($intercalariesIndex < count($intercalaries) &&
                    $intercalaries[$intercalariesIndex]["page"] == $pageNumberIndex + 1) {
                    
                    $pdf->write2DBarcode($intercalaries[$intercalariesIndex]["qrcode"], 'QRCODE,L', 20, 30, 50, 50);
                    $pdf->Text(20, 25, $intercalaries[$intercalariesIndex]["qrcode"]);
                    
                    $intercalariesIndex++;
                    $fragment++;
                } else {
                    $pdf->Text(20, 25, "Morceau " . $fragment);
                }
                
                $pageNumberIndex++;
            }
            
            return $pdf;
            
        } catch (\ReflectionException $e) {
            $this->expectExceptionCode(\ReflectionException::class);
        }
        
        return null;
    }
}
