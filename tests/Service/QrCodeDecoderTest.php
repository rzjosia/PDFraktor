<?php

namespace App\Tests\Service;

use App\Service\QrCodeDecoder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class QrCodeDecoderTest extends KernelTestCase
{
    public function testDecodeWithIntercalaries()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $path = $container->getParameter('kernel.project_dir') . '/tests/Data/document_with_intercalaries.pdf';

        $this->assertFileExists($path);

        $qrCodeDecoder = $container->get(QrCodeDecoder::class);
        $separator = $container->getParameter('separator');
        $intercalaries = $qrCodeDecoder->decode($path)->getIntercalaries($separator);

        $expected = [
            [
                "index" => 1,
                "page" => 2,
                "text" => $separator
            ],
            [
                "index" => 3,
                "page" => 4,
                "text" => $separator
            ]
        ];

        $this->assertTrue($this->identicalArray($expected, $intercalaries));
    }

    public function testDecodeWithoutIntercalaries()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $path = $container->getParameter('kernel.project_dir') . '/tests/Data/document_without_intercalaries.pdf';

        $this->assertFileExists($path);

        $qrCodeDecoder = $container->get(QrCodeDecoder::class);
        $separator = $container->getParameter('separator');
        $intercalaries = $qrCodeDecoder->decode($path)->getIntercalaries($separator);

        $this->assertEmpty($intercalaries);
    }

    private function identicalArray($a, $b): bool
    {
        if (count($a) != count($b)) {
            return false;
        }

        foreach ($a as $k => $v) {
            if (is_array($v) && is_array($b[$k])) {
                if (!$this->identicalArray($v, $b[$k])) {
                    return false;
                }
            } elseif ($v !== $b[$k]) {
                return false;
            }
        }

        return true;
    }
}