<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private object $hasher;
    private array $genders = ['male', 'female'];

    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        for ($i = 1; $i <= 50; $i++) {
            $user = new User();
            $gender = $faker->randomElement($this->genders); //cherche une valeur aleatoire dans un tableau
            $user->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setDisabled($faker->boolean(10))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setLastLogAt(new \DateTimeImmutable())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->hasher->hashPassword($user, 'password'));
                $gender = ($gender == 'male') ? 'm' : 'f';
                $user->setImage('0'.($i + 10). $gender. '.jpg');
                $manager->persist($user);
        }
        $manager->flush();

        $manager->flush();
    }
}
