<?php

namespace App\DataFixtures;

use App\Entity\Level;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LevelFixture extends Fixture
{
    private array $levels = [];
    public function load(ObjectManager $manager): void
    {
        $this->levels = ['débutant', 'Intermédiaire', 'Avancé', 'Expert'];
        $faker  = Factory::create();
        foreach ($this->levels as $level_name) {
            $level = new Level();
            $level->setName($level_name)
            ->setPrerequisite($faker->sentences(5, true));
            $manager->persist($level);
        }

        $manager->flush();
    }
}
