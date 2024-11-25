<?php

namespace App\DataFixtures;

use App\Entity\Basket;
use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Registration;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RegistrationFixture extends Fixture implements DependentFixtureInterface
{
    private array $users;
    private array $courses;
    public function load(ObjectManager $manager): void
    {
        $slug = new Slugify();
        $faker  = Factory::create();
        $this->users = $manager->getRepository(User::class)->findAll();
        $this->courses = $manager->getRepository(Course::class)->findAll();
        foreach ($this->users as $user) {
            for($i=1; $i< $faker->numberBetween(0, 20); $i++) {
                $basket = new Registration();
                $basket->setUser($user)
                    ->setCourse($faker->randomElement($this->courses))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ;
                $manager->persist($basket);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CourseFixture::class
        ];
    }
}
