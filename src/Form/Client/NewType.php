<?php

namespace App\Form\Client;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Faker\Core\DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class NewType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{ $companyId = $options['companyId'];
    $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
        ])


        ->add('phone', TelType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => '/^[0-9]{10}$/']),
            ],
        ])
        ->add('lastName', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('firstName', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('address', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('city', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('postalCode', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('createdAt', DateTimeType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Ce champ ne doit pas être vide.',
                ]),
                new Assert\GreaterThanOrEqual([
                    'value' => 'today',
                    'message' => 'La date doit être égale ou postérieure à aujourd\'hui.',
                ]),
                new Assert\LessThanOrEqual([
                    'value' => 'tomorrow',
                    'message' => 'La date doit être égale ou antérieure à demain.',
                ]),
            ],
            'data' => new \DateTime(),

        ])
        ->add('companyId', EntityType::class, [
            'class' => Company::class,
            'choice_label' => 'name',
            'label' => 'Nom companies',
            'query_builder' => function (CompanyRepository $repository) use ($companyId) {
                return $repository->createQueryBuilder('c')
                    ->andWhere('c.id = :companyId')
                    ->setParameter('companyId', $companyId);
            },
        ]);}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'companyId' => null,]);

    }
}