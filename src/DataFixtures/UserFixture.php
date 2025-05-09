<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}
    public function load(ObjectManager $manager): void
    {
        $admin1 = new User();
        $admin1->setEmail('admin1@gmail.com');
        $admin1->setPassword($this->hasher->hashPassword($admin1,'admin123'));
        $admin1->setRoles(['ROLE_ADMIN']);
        $admin2 = new User();
        $admin2->setEmail('admin2@gmail.com');
        $admin2->setPassword($this->hasher->hashPassword($admin2,'admin1234'));
        $admin2->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin1);
        $manager->persist($admin2);
        for ($i = 1; $i < 4; $i++) {
            $user = new User();
            $user->setEmail("user$i@gmail.com");
            $user->setPassword($this->hasher->hashPassword($user,'user'));
            $manager->persist($user);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
