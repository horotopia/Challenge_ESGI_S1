<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EditProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $companyId = $options['company_id'];
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
                'label' => 'Nom',
            ])
            ->add('description',TextType::class, [
        'constraints' => [
            new Assert\NotBlank(),
            new Assert\Length(['max' => 255]),
        ],
        'label' => 'Description',
    ])
            ->add('brand' ,TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
                'label' => 'Marque',
            ])
            ->add('unitPrice' ,NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
                'label' => 'Prix',
            ])
            ->add('Id' ,NumberType::class, [
                'label_attr' => [
                    'class' => 'hidden', // Classe CSS pour le label
                ],
                'attr' => [
                    'class' => 'hidden',
                ],
            ])

            ->add('VAT' ,NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
                'label' => 'TVA',
            ])
            ->add('availableQuantity' ,NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
                'label' => 'Quantité',
            ])
            ->add('categoryId', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Nom category',
                'query_builder' => function (CategoryRepository $repository) use ($companyId) {
                    return $repository->createQueryBuilder('c')
                        ->andWhere('c.company_id = :companyId')
                        ->setParameter('companyId', $companyId);
                },
            ])
            ->add('Enregistrer',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'company_id' => null,
        ]);
    }
}
