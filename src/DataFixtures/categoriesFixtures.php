<?php

namespace App\DataFixtures;
use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class categoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $faker = Factory::create();

        // for ($i = 0; $i < 15; $i++) {
        //     $categorie =new Categorie();
        //     $categorie->setNom($faker->name);
        //     $categorie->setDescription($faker->text());
            
        //     $manager->persist($categorie);
        // }

        // $manager->flush();
    } 

}