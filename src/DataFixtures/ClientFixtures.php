<?php

namespace App\DataFixtures;
use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**$faker = Factory::create();

        for ($i = 0; $i < 15; $i++) {
            $client =new Client();
            $client->setLastName($faker->firstName);
            $client->setFirstName($faker->lastName);
            $client->setEmail($faker->email);
            $client->setAddress($faker->address);
            $client->setCity($faker->country);
            $client->setPhone('+330751454844');
            $client->setAddress($faker->address);
            $client->setPostalCode('75000');
            $randomDate = $faker->dateTimeBetween('-50 years', 'now');
            $client->setCreatedAt(new \DateTime);
            $client->setUserCreated($faker->firstName);
            $manager->persist($client);
        }

        $manager->flush(); */
    }

}