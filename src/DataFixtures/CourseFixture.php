<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Level;
use Cocur\Slugify\Slugify;
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
        $slug = new Slugify();
        $this->categories = $manager->getRepository(Category::class)->findAll();
        $this->levels = $manager->getRepository(Level::class)->findAll();

        for ($i = 1; $i <= 100; $i++) {
            $course = new Course();
            $course_name = $faker->paragraph();
            $course->setCategory($faker->randomElement($this->categories))
                ->setLevel($faker->randomElement($this->levels))
                ->setName($course_name)
                ->setSmallDescription($faker->text)
                ->setFullDescription($faker->paragraph(100))
                ->setDuration($faker->randomDigit() . 'mois')
                ->setPrice($faker->optional(0.6)->randomNumber(5, false))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setPublished($faker->boolean(80))
                ->setSlug($slug->slugify($course_name))
                ->setImage($faker->optional()->imageUrl());
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
