<?php


namespace App\Service;


class PdfUrlHasher
{
    /**
     * @var string
     */
    private $hashPrefix;
    
    /**
     * @var string
     */
    private $hashAlgo;
    
    /**
     * PdfUrlHasher constructor.
     * @param $hashPrefix
     * @param $hashAlgo
     */
    public function __construct($hashPrefix, $hashAlgo)
    {
        $this->hashPrefix = $hashPrefix;
        $this->hashAlgo = $hashAlgo;
    }
    
    /**
     * @param $url
     * @return string
     */
    public function hashUrl($url) : string {
        return hash($this->hashAlgo, $this->hashPrefix . $url);
    }
    
    /**
     * @param $url
     * @param $hashUrl
     * @return bool
     */
    public function urlHashEquals($url, $hashUrl) : bool
    {
        return hash_equals($this->hashUrl($url), $hashUrl);
    }
}