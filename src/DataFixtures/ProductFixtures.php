<?php

namespace App\DataFixtures;
use App\Entity\Product;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       /* $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $produit = new Product();
            $produit
                ->setName($faker->words(3, true))
                ->setDescription($faker->sentence)
                ->setBrand($faker->company)
                ->setUnitPrice($faker->randomFloat(2, 10, 100))
                ->setVAT($faker->numberBetween(5, 25))
                ->setAvailableQuantity($faker->numberBetween(10, 100))
                ->setCreatedAt($faker->dateTimeThisYear)
                ->setUpdatedAt($faker->dateTimeThisYear)
                ->setUserCreated($faker->userName)
                ->setUserUpdated($faker->userName);

            $manager->persist($produit);
        }

        $manager->flush();*/}
}