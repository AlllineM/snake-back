<?php

namespace App\DataFixtures;

use App\Entity\Reaction;
use App\Entity\UserMate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ReactionFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $user = $this->getReference(UserAlikerFixtures::FACT_AUTHOR_REFERENCE);
        $fact = $this->getReference(FactFixtures::FACT_REFERENCE);

        for ($i = 0; $i < 500; $i++) {
            $like = new Reaction();
            $like->setDateTime(new \DateTime());
            $like->setUserAliker($user);
            $like->setFact($fact);
            $like->setStatus(rand(0, 1));

            $manager->persist($like);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return array(
            UserAlikerFixtures::class,
            FactFixtures::class,
        );
    }
}
