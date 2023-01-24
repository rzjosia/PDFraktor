<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Process;

class QrCodeDecoder
{
    private ?string $xmlContent = null;

    public function __construct($qrserver, private readonly LoggerInterface $logger)
    {
    }

    public function decode(string $path): QrCodeDecoder
    {
        $process = new Process(['/usr/bin/zbarimg', '-q', '--xml', $path]);

        $process->start();
        $process->wait();

        if (!$process->isSuccessful()) {
            $this->logger->error($process->getErrorOutput());
            return $this;
        }

        $this->xmlContent = $process->getOutput();

        return $this;

    }

    public function getIntercalaries(string $separator): array
    {
        if ($this->xmlContent == null) {
            $this->logger->info("no xml content");
            return [];
        }

        $crawler = new Crawler();
        $crawler->addXmlContent($this->xmlContent);

        $this->logger->info("xml content filter begin");

        $output = $crawler->filterXPath('//source/index')->each(static function (Crawler $indexCrawler, $i) use ($separator) {
            $res = $indexCrawler->filterXPath('node()//symbol')->each(static function (Crawler $symbolCrawler, $i) use ($separator, $indexCrawler): ?array {
                $text = $symbolCrawler->filterXPath('node()//data')->text("none");
                if ($text !== $separator) {
                    return null;
                }
                $index = (int)$indexCrawler->filterXPath('node()')->extract(['num'])[0];
                return [
                    "index" => $index,
                    "page" => $index + 1,
                    "text" => $text
                ];
            });
            return $res[0] ?? null;
        });

        $this->logger->info("xml content filter end : " . count($output));

        return $output;
    }
}