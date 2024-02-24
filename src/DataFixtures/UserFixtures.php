<?php

namespace App\DataFixtures;

use App\Entity\Users;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
          $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {

            $user = new Users();
            $user->setLastName($faker->firstName);
            $user->setFirstName($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setPassword('$2y$13$wtS/RWHOWlbkdVABLVQ/2uBCzprrV0nDxtxU1MWNW4sBpwlXdrhmG');
            $randomDate = $faker->dateTimeBetween('-50 years', 'now');
            $user->setCreatedAt(new DateTime());
            $user->setIsVerified(true);
            $user->setRoles(['ROLE_COMPTABLE']);
            $manager->persist($user);
        }

        for ($i = 0; $i < 5; $i++) {

            $user = new Users();
            $user->setLastName($faker->firstName);
            $user->setFirstName($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setPassword('$2y$13$wtS/RWHOWlbkdVABLVQ/2uBCzprrV0nDxtxU1MWNW4sBpwlXdrhmG');
            $randomDate = $faker->dateTimeBetween('-50 years', 'now');
            $user->setCreatedAt(new DateTime());
            $user->setIsVerified(true);
            $user->setRoles(['ROLE_ENTREPRISE']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
