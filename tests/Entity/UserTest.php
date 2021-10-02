<?php 

namespace App\tests\Entity;
use App\Entity\User;

use Symfony\Component\Validator\Validation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

Class UserTest extends KernelTestCase
{

    public function testValidEntity()
    {
        $user = new User();
        $user->setUserName('ludovicTest')
            ->setRoles(['ROLE_USER'])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('testUser@gmail.com')
        ;
        self::bootKernel();
        $validator = Validation::createValidator();
        $error = $validator->validate($user);
        $this->assertCount(0, $error);
    }

    /* public function testInvalidEntity()
    {
        $user = new User();
        $user->setUserName('ludovicTest')
            ->setRoles(['ROLE_USER'])
            ->setPassword('$2y$13$kXV/Wd7Ulh66rf0JebM7C.0W5yWiI8Wofd0wK7gbqxs/G6/6pRDa2')
            ->setEmail('ludovic.mangaj@gmail.com')
        ;
        self::bootKernel();
        $validator = Validation::createValidator();
        $error = $validator->validate($user);
        $this->assertCount(0, $error);
    } */
}
 