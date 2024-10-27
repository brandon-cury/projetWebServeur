<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Level;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CourseFixture extends Fixture implements DependentFixtureInterface
{
    private array $categories = [];
    private array $levels = [];
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $this->categories = $manager->getRepository(Category::class)->findAll();
        $this->levels = $manager->getRepository(Level::class)->findAll();

        for ($i = 1; $i <= 100; $i++) {
            $course = new Course();
            $course->setCategory($faker->randomElement($this->categories))
                ->setLevel($faker->randomElement($this->levels))
                ->setName($faker->paragraph())
                ->setSmallDescription($faker->paragraph())
                ->setFullDescription($faker->text())
                ->setDuration($faker->randomDigit() . 'mois')
                ->setPrice($faker->optional(0.6)->randomNumber(5, false))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setPublished($faker->boolean(80))
                ->setSlug($faker->slug())
                ->setImage($faker->optional()->imageUrl())
                ->setProgram('du lundi au vendredi de 9h à 18h30');
                $manager->persist($course);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixture::class,
            LevelFixture::class
        ];
    }
}
