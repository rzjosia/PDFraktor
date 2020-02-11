<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Process;

class QrCodeDecoder
{
    /**
     * @var string
     */
    private $qrserver;
    
    /**
     * @var string|null
     */
    private $xmlContent;
    
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * QrCodeDecoder constructor.
     * @param $qrserver
     * @param LoggerInterface $logger
     */
    public function __construct($qrserver, LoggerInterface $logger)
    {
        $this->qrserver = $qrserver;
        $this->xmlContent = null;
        $this->logger = $logger;
    }
    
    /**
     * @param string $path
     * @return $this
     */
    public function decode(string $path)
    {
        // FIXME: erreur appel système
        
        $process = new Process(['/usr/bin/zbarimg', '-q', '--xml', $path]);
        
        $process->start();
        $process->wait();
        
        $this->xmlContent = $process->getOutput();
        
        $this->logger->info("output content : " . $process->getErrorOutput());
        
        return $this;
        
    }
    
    /**
     * @param string $separator
     * @return array
     */
    public
    function getIntercalaries(string $separator): array
    {
        if ($this->xmlContent == null) {
            $this->logger->info("no xml content");
            return [];
        }
        
        $crawler = new Crawler();
        $crawler->addXmlContent($this->xmlContent);
        
        $this->logger->info("xml content filter begin");
        
        $output = $crawler->filterXPath('//source/index')->each(function (Crawler $indexCrawler, $i) use ($separator) {
            $res = $indexCrawler->filterXPath('node()//symbol')->each(function (Crawler $symbolCrawler, $i) use ($separator, $indexCrawler) {
                $text = $symbolCrawler->filterXPath('node()//data')->text("none");
                if ($text == $separator) {
                    return [
                        "index" => (int)$indexCrawler->filterXPath('node()')->extract(['num'])[0],
                        "page" => (int)$indexCrawler->filterXPath('node()')->extract(['num'])[0] + 1,
                        "text" => $text
                    ];
                }
                return null;
            });
            
            return array_merge(array_filter($res))[0] ?? null;
        });
        
        $this->logger->info("xml content filter end");
        
        return array_filter($output);
    }
    
    /**
     * @return string
     */
    public
    function getXmlContent(): string
    {
        return $this->xmlContent;
    }
    
}