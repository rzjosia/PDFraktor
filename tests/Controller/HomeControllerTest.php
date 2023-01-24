<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHome(): void
    {
        $kernelBrowser = static::createClient();

        $kernelBrowser->request('GET', '/');

        $this->assertTrue($kernelBrowser->getResponse()->isSuccessful());
    }
}
