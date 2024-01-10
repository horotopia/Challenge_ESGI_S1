<?php

namespace App\Form\clients;

use App\Entity\Entreprise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;


class editTypeForm extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
        ])


        ->add('telephone', TelType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => '/^[0-9]{10}$/']),
            ],
        ])
        ->add('nom', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('prenom', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('adresse', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('ville', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('code_postal', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ])
        ->add('create_at', DateTimeType::class, [
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
        ->add('id_entreprise', EntityType::class, [
            'class' => Entreprise::class,
            'choice_label' => 'nom',
            'label' => 'Nom entreprise',

        ]);}
}