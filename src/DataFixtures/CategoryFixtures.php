<?php

namespace App\DataFixtures;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $faker = Factory::create();

        // for ($i = 0; $i < 15; $i++) {
        //     $categories =new Category();
        //     $categories->setNom($faker->name);
        //     $categories->setDescription($faker->text());
            
        //     $manager->persist($categories);
        // }

        // $manager->flush();
    } 

}