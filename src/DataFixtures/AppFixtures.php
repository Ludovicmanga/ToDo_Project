<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Users

         $adminUser = new User();
         $adminUser->setUserName('Ludovic')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('ludovic.mangaj@gmail.com')
        ;

        $manager->persist($adminUser);

        $nonAdminUser = new User();
        $nonAdminUser->setUserName('NonUser')
            ->setRoles([])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('victime@ipt.fr')
        ;

        $manager->persist($nonAdminUser);

        $userToEdit = new User();
        $userToEdit->setUserName('userToEdit')
            ->setRoles([])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('userToEdit@gmail.com')
        ;
        $manager->persist($userToEdit);

        $userToDelete = new User();
        $userToDelete->setUserName('userToDelete')
            ->setRoles([])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('userToDelete@gmail.com')
        ;
        $manager->persist($userToDelete);

        // Tasks 

        $taskToEdit = new Task();
        $taskToEdit->setTitle('taskToEdit')
                   ->setContent('Test content')
                   ->setIsDone(true)
                   ->setUser($adminUser)
        ;
        $manager->persist($taskToEdit); 

        $taskToDelete = new Task();
        $taskToDelete->setTitle('taskToDelete')
                   ->setContent('Test content')
                   ->setIsDone(true)
                   ->setUser($adminUser)
        ;
        $manager->persist($taskToDelete);

        $taskToToggle = new Task();
        $taskToToggle->setTitle('taskToToggle')
                   ->setContent('Test content')
                   ->setIsDone(true)
                   ->setUser($adminUser)
        ;
        
        $manager->persist($taskToToggle);

        $manager->flush();
    }
}
