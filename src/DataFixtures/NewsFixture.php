<?php

namespace App\DataFixtures;

use App\Entity\News;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NewsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker  = Factory::create();
        $slug = new Slugify();
        for($i=1; $i<=100; $i++){
            $new = new News();
            $name = $faker->unique()->name();

            $new->setName($name)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setSlug($slug->slugify($name))
                ->setPublished($faker->boolean(70))
                //->setImage($faker->boolean(70)?'https://picsum.photos/480/240':null)
                ->setContent($faker->paragraph);
                $manager->persist($new);
        }

        $manager->flush();
    }
}
