<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixture extends Fixture
{
    private array $categories = [];
    public function load(ObjectManager $manager): void
    {
        $slug = new Slugify();
        $this->categories = [
            ['name'=>'PHP', 'image'=>'php.png'],
            ['name'=>'Laravel', 'image'=>'laravel.png'],
            ['name'=>'Javascript', 'image'=> 'javascript.png'],
            ['name'=>'React', 'image'=>'react.png']];
        $faker  = Factory::create();
        foreach ($this->categories as $category_array) {
            $category = new Category();
            $category->setName($category_array['name'])
                ->setDescription($faker->realText())
                ->setImage($category_array['image'])
                ->setSlug($slug->slugify($category_array['name']));
            $manager->persist($category);
        }

        $manager->flush();
    }
}
