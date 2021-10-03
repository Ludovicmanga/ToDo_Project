<?php

namespace App\tests;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class DefaultControllerTest extends WebTestCase
{
    public function testResponseIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDisplayIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSelectorExists('.create_task');
        $this->assertSelectorExists('.bienvenue');
    }
}
