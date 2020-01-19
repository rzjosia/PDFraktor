<?php


namespace App\Service;


use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Process;

class QrCodeDecoder
{
    /**
     * @var string
     */
    private $qrserver;
    
    /**
     * @var string
     */
    private $xmlContent;
    
    /**
     * QrCodeDecoder constructor.
     * @param $qrserver
     */
    public function __construct($qrserver)
    {
        $this->qrserver = $qrserver;
    }
    
    /**
     * @param string $path
     * @return $this
     */
    public function decode(string $path): self
    {
        $process = new Process(['/usr/bin/zbarimg', '-q', '--xml', $path]);
        $process->start();
        $process->wait();
        $this->xmlContent = $process->getOutput();
        return $this;
    }
    
    /**
     * @param string $separator
     * @return array
     */
    public function getIntercalaries(string $separator): array
    {
        $crawler = new Crawler();
        $crawler->addXmlContent($this->xmlContent);
        
        return $crawler->filterXPath('//source/index')->each(function (Crawler $indexCrawler, $i) use ($separator) {
            $text = $indexCrawler->filterXPath('node()//data')->text("none");
            if ($text == $separator) {
                return [
                    "index" => (int)$indexCrawler->filterXPath('node()')->extract(['num'])[0],
                    "text" => $text
                ];
            }
            return false;
        });
    }
    
    /**
     * @return string
     */
    public function getXmlContent(): string
    {
        return $this->xmlContent;
    }
    
    
}