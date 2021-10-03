<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class TaskControllerTest extends WebTestCase
{
    public function createValidTask($client, $title, $content)
    {
        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => $title,
            'task[content]' => $title
        ]);
        $client->submit($form);
    }

    public function testListAction()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('h1', 'Liste des tâches');
    }

    public function testCreateTask()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $this->createValidTask($client, 'test Title', 'Test Content');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

     public function testCreateActionWhileNotLoggedIn()
    {
        $client = static::createClient();

        //We don't log the user in
        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Test Task While unlogged',
            'task[content]' => 'Test Content'
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(500);
    }

     public function testValidEditTask()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/17/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Test Edited Task',
            'task[content]' => 'Test Edited Content'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');  
    }

     public function testEditTaskWhenNotProprietary()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('victime@ipt.fr');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/17/edit');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

     public function testValidToggleTask()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToToggle = $taskRepository->findOneByTitle('taskToToggle');

        if($taskToToggle->getIsDone() === false) {
            $crawler = $client->request('GET', '/tasks/'.$taskToToggle->getId().'/toggle');
            $this->assertResponseRedirects();
            $client->followRedirect();
            $this->assertEquals($taskToToggle->getIsDone(), true);
        } elseif($taskToToggle->getIsDone() === true) {
            $crawler = $client->request('GET', '/tasks/'.$taskToToggle->getId().'/toggle');
            $this->assertResponseRedirects();
            $client->followRedirect();
            $this->assertEquals($taskToToggle->getIsDone(), false);
        }
    } 

     public function testToggleTaskWhenNotProprietary()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('victime@ipt.fr');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/17/edit');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testDeleteTaskWhenNotProprietary()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('victime@ipt.fr');
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/17/delete');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

      public function testValidDeleteTask()
    {
        $client = static::createClient();

        //We log the user in
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('ludovic.mangaj@gmail.com');
        $client->loginUser($testUser);

        // We delete it
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToDelete = $taskRepository->findOneByTitle('taskToDelete');
        $crawler = $client->request('POST', '/tasks/'.$taskToDelete->getId().'/delete');
        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');

        // We create it back
        $this->createValidTask($client, 'taskToDelete', 'test content');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
