<?php

namespace App\Form\category;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => "Nom",
                'error_bubbling' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Ce champ ne doit pas être vide.',
                    ]),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('description',TextType::class,[
                'label' => "Description",
                'error_bubbling' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Ce champ ne doit pas être vide.',
                    ]),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('Enregistrer',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
