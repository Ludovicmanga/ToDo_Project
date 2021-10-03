<?php

namespace App\tests;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class UserControllerTest extends WebTestCase
{
     public function testAdminCanAccessUserList()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $client->request('GET', '/user/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');
    }

    public function testNonAdminCannotAccessUserList()
    {
        $client = static::createClient();

        $client->request('GET', '/user/');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.login');
    }

    public function testLetAdminAccessUserManagement()
    {
        $client = static::createClient();

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);
        // user is now logged in, so you can test protected resources
        $client->request('GET', '/user/');
        $this->assertResponseIsSuccessful();
    }

    public function testUserManagementRequireAdminRole()
    {
        $client = static::createClient();

        // get or create the user somehow (e.g. creating some users only
        // for tests while loading the test fixtures)
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('victime@ipt.fr');
        $client->loginUser($testUser);
        // user is now logged in, so you can test protected resources
        $client->request('GET', '/user/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
 
    public function testCreateUser()
    {
        $client = static::createClient();

        // We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/user/new');
        $form = $crawler->selectButton('Enregistrer')->form([
            'user[username]' => 'Test Ludovic',
            'user[password]' => '2707',
            'user[email]' => 'ludovicTest@gmail.com',
            'user[roles]' => 'ROLE_USER'
        ]);

        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');

        // After creation, we delete it
        $userCreated = $userRepository->findOneByEmail('ludovicTest@gmail.com');
        $crawler = $client->request('POST', '/user/'.$userCreated->getId());
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');
    } 

    public function testShow()
    {
        $client = static::createClient();

        // We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $client->request('GET', '/user/14');
        $this->assertSelectorExists('h1', 'Détail de l\'utilisateur');
    }

    public function testEdit()
    {
        $client = static::createClient();

        // We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $userToEdit = $userRepository->findOneByEmail('userToEdit@gmail.com');
        
        $crawler = $client->request('GET', '/user/'.$userToEdit->getId().'/edit');
        $this->assertSelectorExists('h1', 'Modifier l\'utilisateur');
        $form = $crawler->selectButton('Mettre à jour')->form([
            'user[username]' => 'userToEdit',
            'user[password]' => '2707',
            'user[email]' => 'userEdited@gmail.com',
            'user[roles]' => 'ROLE_USER'
        ]);
        $client->submit($form);
        
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');

        //We put it back to normal
        $userEdited = $userRepository->findOneByEmail('userEdited@gmail.com');
        $crawler = $client->request('GET', '/user/'.$userEdited->getId().'/edit');
        $this->assertSelectorExists('h1', 'Modifier l\'utilisateur');

        $form = $crawler->selectButton('Mettre à jour')->form([
            'user[username]' => 'userToEdit',
            'user[password]' => '2707',
            'user[email]' => 'userToEdit@gmail.com',
            'user[roles]' => 'ROLE_USER'
        ]);

        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');
    }

    public function testDeleteUser()
    {
        $client = static::createClient();

        // We log the user as an admin
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        //We delete it
        
        $userToDelete = $userRepository->findOneByEmail('userToDelete@gmail.com');
        $crawler = $client->request('POST', '/user/'.$userToDelete->getId());
        $this->assertResponseRedirects();

        // After deletion, we create it back

        $crawler = $client->request('GET', '/user/new');
        $form = $crawler->selectButton('Enregistrer')->form([
            'user[username]' => 'userToDelete',
            'user[password]' => '2707',
            'user[email]' => 'userToDelete@gmail.com',
            'user[roles]' => 'ROLE_USER'
        ]);

        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');
    }
}
