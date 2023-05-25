<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationTest extends WebTestCase
{
    public function testApplicationCanRender(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h3', 'op Richest Teams');
    }
}
