<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $user1 = new User();
         $user1->setUserName('Ludovic')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('ludovic.mangaj@gmail.com')
        ;

        $manager->persist($user1);

        $user2 = new User();
        $user2->setUserName('Victime')
            ->setRoles([])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('victime@ipt.fr')
        ;

        $manager->persist($user2);

        $user3 = new User();
        $user3->setUserName('userToEdit')
            ->setRoles([])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('userToEdit@gmail.com')
        ;

        $manager->persist($user3);

        $manager->flush();
    }
}
