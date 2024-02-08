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
           $faker = Factory::create();

         for ($i = 0; $i < 5; $i++) {
             $company = new Company();
             $company->setName($faker->company);
             $company->setEmail($faker->email);
             $company->setSiret($this->generateSiret($faker));
             $company->setDescription($faker->text);
             $company->setPhone('+330751454844');
             $company->setAddress($faker->address);
             $company->setPostalCode('75000');
             $company->setCountry($faker->country);
             $randomDate = $faker->dateTimeBetween('-50 years', 'now');
             $company->setCreatedAt(\DateTimeImmutable::createFromMutable($randomDate));
             $manager->persist($company);
         }

         $manager->flush();
     }

     private function generateSiret($faker): string
     {
         $siret = '';

         for ($i = 0; $i < 14; $i++) {
             $siret .= $faker->randomDigit;
         }

         return $siret;
     }
}
