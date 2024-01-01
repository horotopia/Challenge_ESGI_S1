<?php

namespace App\DataFixtures;

use App\Entity\Entreprise;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EnterpriseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $entreprise = new Entreprise();
            $entreprise->setNom($faker->company);
            $entreprise->setEmail($faker->email);
            $entreprise->setSiret($this->generateSiret($faker));
            $entreprise->setDescription($faker->text);
            $entreprise->setTelephone('+330751454844');
            $entreprise->setAdresse($faker->address);
            $entreprise->setCodePostal('75000');
            $entreprise->setPays($faker->country);
            $randomDate = $faker->dateTimeBetween('-50 years', 'now');
            $entreprise->setCreateAt(\DateTimeImmutable::createFromMutable($randomDate));
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

        return $siret;
    }
}
