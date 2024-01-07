<?php

namespace App\DataFixtures;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class clientsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 15; $i++) {
            $client =new Client();
            $client->setNom($faker->firstName);
            $client->setPrenom($faker->lastName);
            $client->setEmail($faker->email);
            $client->setAdresse($faker->address);
            $client->setVille($faker->country);
            $client->setTelephone('+330751454844');
            $client->setAdresse($faker->address);
            $client->setCodePostal('75000');
            $randomDate = $faker->dateTimeBetween('-50 years', 'now');
            $client->setCreateAt(\DateTimeImmutable::createFromMutable($randomDate));
            $client->setUserCreate($faker->firstName);
            $manager->persist($client);
        }

        $manager->flush();
    }

}