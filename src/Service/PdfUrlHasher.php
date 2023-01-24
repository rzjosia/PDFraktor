<?php


namespace App\Service;


class PdfUrlHasher
{
    public function __construct(private readonly string $hashPrefix, private readonly string $hashAlgo)
    {
    }

    public function hashUrl($url) : string {
        return hash($this->hashAlgo, $this->hashPrefix . $url);
    }

    public function urlHashEquals($url, $hashUrl) : bool
    {
        return hash_equals($this->hashUrl($url), $hashUrl);
    }
}