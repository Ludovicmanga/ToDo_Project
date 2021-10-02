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
        $this->assertSelectorExists('a', 'Créer une nouvelle tâche');
        $this->assertSelectorExists('h1', 'Bienvenue sur Todo List');
    }
}