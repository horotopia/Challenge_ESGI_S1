<?php

namespace App\Form\Quote;

use App\Entity\Client;
use App\Entity\Product;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Updated this line
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {$companyId = $options['companyId'];
        $builder
            ->add('dueDate', DateType::class, ['data' => new \DateTime(),'widget' => 'single_text'])
            ->add('productId', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un product',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'products-select',
                ],
                'query_builder' => function (ProductRepository $repository) use ($companyId) {
                    return $repository->createQueryBuilder('p')
                        ->andWhere('p.companyId = :companyId')
                        ->orderBy('p.name', 'ASC')
                        ->setParameter('companyId', $companyId);
                },
            ])

            ->add('availableQuantity', IntegerType::class, [
                'required' => false,
                'data' => 0,
            ])
            ->add('unitPrice', TextType::class, [
                'required' => true,
                'disabled' => true,
            ])
            ->add('VAT', TextType::class, [
                'required' => true,
                'disabled' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Brouillon' => 'Brouillon',
                ],
            ])
            ->add('clientId', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getLastName() . ' ' . $client->getFirstName();
                },
                'query_builder' => function (ClientRepository $repository) use ($companyId) {
                    return $repository->createQueryBuilder('c')
                        ->andWhere('c.companyId = :companyId')
                        ->orderBy('c.lastName', 'ASC')
                        ->addOrderBy('c.firstName', 'ASC')
                        ->setParameter('companyId', $companyId);
                },
            ]);
    }

public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'companyId' => null,]);

}}