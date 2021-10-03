<?php 

namespace App\tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Validator\Validation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

Class TaskTest extends KernelTestCase
{
    public function hydrateUser($user, $id, $userName, $password, $email, $role)
    {
        $user->setId($id)
             ->setUserName($userName)
             ->setPassword($password)
             ->setEmail($email)
             ->setRoles($role)
        ;
    }

    public function hydrateTask(Task $task, $title, $content, $isDone, $user, $createdAt)
    {
        $task->setTitle($title)
             ->setContent($content)
             ->setIsDone($isDone)
             ->setUser($user)
             ->setCreatedAt($createdAt)
        ;
    }

    public function testValidEntity()
    {   $user = new User();
        $this->hydrateUser($user, 1000, 'Test', '$2y$13$mPfu/S76OECtDp0m0gAysu6HC3kDh71h3H8QgE11WUFkW12j27ph6', 'test@test.fr', ['ROLE_USER']);
        $task = new Task();
        $this->hydrateTask($task, 'test1', 'content test 1', 'content test 1', null, $user, New \DateTime());
        self::bootKernel();
        $validator = Validation::createValidator();
        $error = $validator->validate($task);
        $this->assertCount(0, $error);
    }
}
