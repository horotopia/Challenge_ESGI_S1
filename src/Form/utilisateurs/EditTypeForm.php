<?php

namespace App\Form\utilisateurs;

use App\Entity\Entreprise;
use App\Entity\Users;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EditTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email' ,EmailType::class,[
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles admin',
                'data' => ['ROLE_COMPTABLE'],
                'label_attr' => ['class' => 'text-base mt-3'],
                'choices' => [
                    'Comptable' => 'ROLE_COMPTABLE',
                    'Enterprise' => 'ROLE_ENTERPRISE',
                ],
                'expanded' => false,
                'multiple' => true,
                'attr' => ['class' => 'select2']
                ,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Count(['min' => 1]),
                ],
            ])

        ->add('telephone',TelType::class)
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('created_at', DateTimeType::class,[
                'label'=>'Date crèation',
            ])
            ->add('id_entreprise', EntityType::class, [
                'class' => Entreprise::class,
                'choice_label' => 'nom',
                'label'=>'Nom enterprise',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
