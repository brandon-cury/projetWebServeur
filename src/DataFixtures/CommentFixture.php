<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    private array $courses;
    private array $users;

    public function load(ObjectManager $manager): void
    {
        $this->courses = $manager->getRepository(Course::class)->findAll();
        $this->users = $manager->getRepository(User::class)->findAll();
        $faker= Factory::create();
        foreach ($this->courses as $course){
            for($i=1; $i<= $faker->numberBetween(2, 10); $i++){
                $comment = new Comment();
                $comment->setContent($faker->paragraph)
                    ->setPublished($faker->boolean(80))
                    ->setRating($faker->numberBetween(0, 5))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setCourse($course)
                    ->setUser($faker->randomElement($this->users));
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CourseFixture::class,
            UserFixtures::class
        ];
    }
}
