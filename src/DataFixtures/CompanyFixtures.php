<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyFixtures extends Fixture
{
   public function load(ObjectManager $manager): void
    {
          /* $faker = Factory::create();

         for ($i = 0; $i < 5; $i++) {
             $entreprise = new Company();
             $entreprise->setName($faker->company);
             $entreprise->setEmail($faker->email);
             $entreprise->setSiret($this->generateSiret($faker));
             $entreprise->setDescription($faker->text);
             $entreprise->setPhone('+330751454844');
             $entreprise->setAddress($faker->address);
             $entreprise->setPostalCode('75000');
             $entreprise->setCountry($faker->country);
             $randomDate = $faker->dateTimeBetween('-50 years', 'now');
             $entreprise->setCreatedAt(\DateTimeImmutable::createFromMutable($randomDate));
             $manager->persist($entreprise);
         }

         $manager->flush();
     }

     private function generateSiret($faker): string
     {
         $siret = '';

         for ($i = 0; $i < 14; $i++) {
             $siret .= $faker->randomDigit;
         }

         return $siret;*/
     }
}
