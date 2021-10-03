<?php

namespace App\tests;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class SecurityControllerTest extends WebTestCase
{
    public function testDisplayLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('button', 'Se connecter');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testSuccessfulLogin()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('login')->form([
            '_username' => 'Ludovic',
            '_password' => '2707'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.logout');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('login')->form([
            '_username' => 'fakeUser',
            '_password' => 'fakePassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.login');
    }

    public function testLogout()
    {
        $client = static::createClient();

        // We log the user as an admin
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $client->request('GET', '/logout');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.login');
    }
}
