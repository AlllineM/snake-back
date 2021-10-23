<?php

namespace App\DataFixtures;

use App\Entity\Fact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class FactFixtures extends Fixture implements DependentFixtureInterface
{
    public const FACT_REFERENCE = 'fact';

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $user = $this->getReference(UserAlikerFixtures::FACT_AUTHOR_REFERENCE);

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $data = $serializer->decode(file_get_contents(__DIR__ . '/../../var/facts.csv'), 'csv');

        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                //пока пропускаем название объекта и теги
                //todo: Обработать их потом
                if ($key == '' || $key == 'тэги') continue;

                //пропускаем пустые строки. Это не костыль)
                if ($value == '') continue;

                $fact = new Fact();
                $fact->setDateTime(new \DateTime());
                $fact->setBody($value);
                $fact->setAuthor($user);
                $manager->persist($fact);
            }
        }
        $manager->flush();
        $this->addReference(self::FACT_REFERENCE, $fact);
    }


    public function getDependencies()
    {
        return array(
            UserAlikerFixtures::class,
        );
    }
}
