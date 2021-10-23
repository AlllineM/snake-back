<?php

namespace App\DataFixtures;

use App\Entity\UserAliker;
use App\Entity\UserMate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class UserAlikerFixtures extends Fixture
{
    public const FACT_AUTHOR_REFERENCE = 'author';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 25; $i++) {
            $user = new UserAliker();
            $user->setNick($faker->firstName);
            $user->setPhone($faker->phoneNumber);
            $user->setPassword($faker->password);
            $user->setBalance(rand(0, 123) * 10);
            $user->setRating(rand(0, 150));
            $user->setRoles(['ROLE_USER']);
            $user->setApiToken(bin2hex(random_bytes(15)));

            $manager->persist($user);
        }
        $this->addReference(self::FACT_AUTHOR_REFERENCE, $user);
        $manager->flush();
    }
}
