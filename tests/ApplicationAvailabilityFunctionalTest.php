<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     * @param $url
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    /**
     * @dataProvider url404
     * @param $url
     */
    public function testPageIsNotFound($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        
        $this->assertTrue($client->getResponse()->isNotFound());
    }
    
    public function urlProvider()
    {
        yield ['/'];
        yield ['/mentions-legales'];
        yield ['/files/no_content'];
    }
    
    public function url404()
    {
        yield ['/uploads'];
        yield ['/files'];
    }
}