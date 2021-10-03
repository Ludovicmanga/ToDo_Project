<?php

namespace App\tests;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class UserControllerTest extends WebTestCase
{
    /**
     * It is not a test function
     * Used in order to perform the tests with a logged user
     */
    public function logTheUserIn($email, $client)
    {
        $userRepository = static::$container->get(UserRepository::class);
        $userToLogIn = $userRepository->findOneByEmail($email);
        $client->loginUser($userToLogIn);
    }

    /**
     * It is not a test function
     * Used to fill the user creation or edition form
     */
    public function createOrEditUserWithForm($client, $crawler, $button, $username, $password, $email, $role)
    {
        $form = $crawler->selectButton($button)->form([
            'user[username]' => $username,
            'user[password]' => $password,
            'user[email]' => $email,
            'user[roles]' => $role
        ]);
        $client->submit($form);
    }

     public function testAdminCanAccessUserList()
    {
        $client = static::createClient();

        //We log the user in
        //$userRepository = static::$container->get(UserRepository::class);
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        $client->request('GET', '/user/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');
    }

    public function testUserManagementRequireAuthentication()
    {
        $client = static::createClient();
        $client->request('GET', '/user/');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.login');
    }

    public function testUserManagementRequireAdminRole()
    {
        $client = static::createClient();

        $this->logTheUserIn('victime@ipt.fr', $client);

        $client->request('GET', '/user/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testLetAdminAccessUserManagement()
    {
        $client = static::createClient();

        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        // user is now logged in, so you can test protected resources
        $client->request('GET', '/user/');
        $this->assertResponseIsSuccessful();
    }
 
    public function testCreateUser()
    {
        $client = static::createClient();

        // We log the user in
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        $crawler = $client->request('GET', '/user/new');
        $this->createOrEditUserWithForm($client, $crawler, 'Enregistrer','Test Ludovic','2707','ludovicTest@gmail.com', 'ROLE_USER');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.user_list');

        // After creation, we delete it
        $userRepository = static::$container->get(UserRepository::class);
        $userCreated = $userRepository->findOneByEmail('ludovicTest@gmail.com');
        $crawler = $client->request('POST', '/user/'.$userCreated->getId());

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.user_list');
    } 

    public function testShow()
    {
        $client = static::createClient();

        // We log the user in
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        $client->request('GET', '/user/14');
        $this->assertSelectorExists('h1', 'Détail de l\'utilisateur');
    }

    public function testEdit()
    {
        $client = static::createClient();

        // We log the user in
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        // We edit the user
        $userRepository = static::$container->get(UserRepository::class);
        $userToEdit = $userRepository->findOneByEmail('userToEdit@gmail.com');
        $crawler = $client->request('GET', '/user/'.$userToEdit->getId().'/edit');
        $this->assertSelectorExists('h1', 'Modifier l\'utilisateur');
        $this->createOrEditUserWithForm($client, $crawler, 'Mettre à jour','userToEdit','2707','userEdited@gmail.com', 'ROLE_USER');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.user_list');

        //We put it back to normal
        $userRepository = static::$container->get(UserRepository::class);
        $userEdited = $userRepository->findOneByEmail('userEdited@gmail.com');
        $crawler = $client->request('GET', '/user/'.$userEdited->getId().'/edit');

        $this->assertSelectorExists('h1', 'Modifier l\'utilisateur');
        $this->createOrEditUserWithForm($client, $crawler, 'Mettre à jour','userToEdit','2707','userToEdit@gmail.com', 'ROLE_USER');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.user_list');
    }

    public function testDeleteUser()
    {
        $client = static::createClient();

        // We log the user as an admin
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        //We delete it
        $userRepository = static::$container->get(UserRepository::class);
        $userToDelete = $userRepository->findOneByEmail('userToDelete@gmail.com');
        $crawler = $client->request('POST', '/user/'.$userToDelete->getId());
        $this->assertResponseRedirects();

        // After deletion, we create it back

        $crawler = $client->request('GET', '/user/new');
        $this->createOrEditUserWithForm($client, $crawler, 'Enregistrer','userToDelete','2707','userToDelete@gmail.com', 'ROLE_USER');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.user_list');
    }
}
