<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $pwd = 'test';

        $user = (new Users())
        ->setIdEntreprise(1)
            ->setEmail('user@user.fr')
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(true)
        ;
        $user->setPassword($this->passwordHasher->hashPassword($user, $pwd));
        $manager->persist($user);

        $user = (new Users())
        ->setIdEntreprise(2)
            ->setEmail('coordinator@user.fr')
            ->setRoles(['ROLE_COORDINATOR'])
            ->setIsVerified(true)
        ;
        $user->setPassword($this->passwordHasher->hashPassword($user, $pwd));
        $manager->persist($user);

        $user = (new Users())
            ->setIdEntreprise(3)
            ->setEmail('admin@user.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true)
        ;
        $user->setPassword($this->passwordHasher->hashPassword($user, $pwd));
        $manager->persist($user);

        for ($i = 0; $i < 10; ++$i) {
            $user = (new Users())
                ->setIdEntreprise(4)
                ->setEmail($faker->email())
                ->setRoles([])
                ->setIsVerified(true)
            ;
            $user->setPassword($this->passwordHasher->hashPassword($user, $pwd));

            $manager->persist($user);
        }

        $manager->flush();
    }
}