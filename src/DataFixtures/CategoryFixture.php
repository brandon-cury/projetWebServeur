<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixture extends Fixture
{
    private array $categories = [];
    public function load(ObjectManager $manager): void
    {
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
                ->setImage($category_array['image']);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
