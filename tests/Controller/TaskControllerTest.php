<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class TaskControllerTest extends WebTestCase
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
     * Used to fill the task creation or edition form
     */
     public function createOrEditTaskWithForm($client, $crawler, $button, $title, $content)
    {
        $form = $crawler->selectButton($button)->form([
            'task[title]' => $title,
            'task[content]' => $content
        ]);
        $client->submit($form);
    } 

    public function testListAction()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.task_list');
    }

    public function testCreateTask()
    {
        $client = static::createClient();

        //We log the user in
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        $crawler = $client->request('GET', '/tasks/create');
        $this->createOrEditTaskWithForm($client, $crawler, 'Ajouter', 'test Title', 'test Content');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorExists('.task_list');
    }

     public function testCreateTaskWhileNotLoggedIn()
    {
        $client = static::createClient();

        //We don't log the user in
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.login');
    }

     public function testValidEditTask()
    {
        $client = static::createClient();

        //We log the user in
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);
        
        //We edit the task
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToEdit = $taskRepository->findOneByTitle('taskToEdit');
        $crawler = $client->request('GET', '/tasks/'.$taskToEdit->getid().'/edit');
        $this->createOrEditTaskWithForm($client, $crawler, 'Modifier', 'Test Edited Task', 'Test Edited Content');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

         //We put it back to normal
         $taskEdited = $taskRepository->findOneByTitle('Test Edited Task');
         $crawler = $client->request('GET', '/tasks/'.$taskEdited->getId().'/edit');
 
         $this->assertSelectorExists('.task_edit');
         $this->createOrEditTaskWithForm($client, $crawler,'Modifier', 'taskToEdit','Test content');
         $this->assertResponseRedirects();
         $client->followRedirect();
         $this->assertSelectorExists('.task_list');
    }

     public function testEditTaskWhenNotProprietary()
    {
        $client = static::createClient();

        //We log the user in
        $this->logTheUserIn('victime@ipt.fr', $client);
        
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToEdit = $taskRepository->findOneByTitle('taskToEdit');
        $crawler = $client->request('GET', '/tasks/'.$taskToEdit->getid().'/edit');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

     public function testValidToggleTask()
    {
        $client = static::createClient();

        //We log the user in
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToToggle = $taskRepository->findOneByTitle('taskToToggle');

        //If the button is marked as undone, we toggle it and make sure it is now done. Or the contrary if it is already marked as done.
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
        $this->logTheUserIn('victime@ipt.fr', $client);

        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToToggle = $taskRepository->findOneByTitle('taskToToggle');
        $crawler = $client->request('GET', '/tasks/'.$taskToToggle->getId().'/toggle');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testDeleteTaskWhenNotProprietary()
    {
        $client = static::createClient();

        //We log the user in
        $this->logTheUserIn('victime@ipt.fr', $client);

        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToDelete = $taskRepository->findOneByTitle('taskToDelete');
        $crawler = $client->request('GET', '/tasks/'.$taskToDelete->getId().'/delete');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

      public function testValidDeleteTask()
    {
        $client = static::createClient();

        //We log the user in
        $this->logTheUserIn('ludovic.mangaj@gmail.com', $client);

        // We delete it
        $taskRepository = static::$container->get(TaskRepository::class);
        $taskToDelete = $taskRepository->findOneByTitle('taskToDelete');
        $crawler = $client->request('POST', '/tasks/'.$taskToDelete->getId().'/delete');
        $client->followRedirect();
        $this->assertSelectorExists('h1', 'Liste des utilisateurs');

        // We create it back
        $crawler = $client->request('GET', '/tasks/create');
        $this->createOrEditTaskWithForm($client, $crawler, 'Ajouter', 'taskToDelete', 'taskToDelete');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
