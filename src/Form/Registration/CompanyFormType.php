<?php

namespace App\Form\Registration;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;


class CompanyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
            'label'=>'Nom entrprise'])

            ->add('siret', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 14,
                        'max' => 14,
                        'exactMessage' => 'Le numéro SIRET doit contenir exactement 14 chiffres',
                    ]),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Company::class
        ]);
    }

}