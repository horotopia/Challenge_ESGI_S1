<?php

namespace App\Form\enterprise;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Country;


class EditTypeForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un nom']),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'Le nom doit avoir au moins {{ limit }} caractères', 'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères']),
                ],
            ])
            ->add('siret', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro SIRET est obligatoire']),
                    new Regex(['pattern' => '/^[0-9]{14}$/', 'message' => 'Le numéro SIRET doit contenir exactement 14 chiffres']),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse email']),
                    new Email(['message' => 'Veuillez saisir une adresse email valide']),
                ],
            ])
            ->add('site_internet', UrlType::class, [
                'constraints' => [
                    new Url(['message' => 'Veuillez saisir une URL valide']),
                ],
            ])
            ->add('telephone', TelType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un numéro de téléphone']),
                    new Regex(['pattern' => '#(?:\+?33|0)[1-9](?:(?:\s|-)?\d{2}){4}#', 'message' => 'Veuillez saisir un numéro de téléphone valide au format international']),
                ],
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse']),
                    new Length(['min' => 2, 'max' => 255, 'minMessage' => 'L\'adresse doit avoir au moins {{ limit }} caractères', 'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères']),
                ],
            ])
            ->add('code_postal', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un code postal']),
                    new Length(['min' => 5, 'max' => 10, 'minMessage' => 'Le code postal doit avoir au moins {{ limit }} chiffres', 'maxMessage' => 'Le code postal ne peut pas dépasser {{ limit }} chiffres']),
                ],
            ])
            ->add('compte_bancaire', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un numéro de compte bancaire']),
                    new Regex(['pattern' => '/^[0-9]{11,34}$/', 'message' => 'Le numéro de compte bancaire doit contenir entre 11 et 34 chiffres']),
                ],
            ])
            ->add('logo', FileType::class, [
                'required' => false,
                'data_class' => null,
                ])
            ->add('pays', CountryType::class, [
                'constraints' => [
                    new Country(['message' => 'Veuillez sélectionner un pays valide']),
                ],
            ]);
    }

}