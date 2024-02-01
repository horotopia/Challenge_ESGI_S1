<?php

namespace App\DataFixtures;
use App\Entity\Produit;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $produit = new Produit();
            $produit
                ->setNom($faker->words(3, true))
                ->setDescription($faker->sentence)
                ->setMarque($faker->company)
                ->setPrixUnitaire($faker->randomFloat(2, 10, 100))
                ->setTva($faker->numberBetween(5, 25))
                ->setQuantiteDisponible($faker->numberBetween(10, 100))
                ->setCreateAt($faker->dateTimeThisYear)
                ->setUpdateAt($faker->dateTimeThisYear)
                ->setUserCreate($faker->userName)
                ->setUserUpdate($faker->userName);

            $manager->persist($produit);
        }

        $manager->flush();}
}