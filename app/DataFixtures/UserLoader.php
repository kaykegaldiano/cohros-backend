<?php

namespace App\DataFixtures;

use App\Model\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserLoader implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('testuser@test.com');
        $user->setPassword('password');

        $manager->persist($user);

        $manager->flush();
    }
}
