<?php

namespace App\Form\devis;

use App\Entity\Client;
use App\Entity\Produit;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddTypeForm extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_echeance', DateType::class,['data' => new \DateTime(),])
            ->add('id_produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un produit',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'produit-select',
                ],
            ])
            ->add('quantite_disponible', IntegerType::class, [
                'required' => false,
                'data'=>0
            ])
            ->add('prix_unitaire', TextType::class, [
                'required' => true,
                'disabled' => true,
            ])
            ->add('tva', TextType::class, [
                'required' => true,
                'disabled' => true,
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Brouillon' => 'Brouillon',

                ],
            ])
            ->add('id_client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getNom() . ' ' . $client->getPrenom();
                },
                'query_builder' => function (ClientRepository $repository) {
                    return $repository->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC')
                        ->addOrderBy('c.prenom', 'ASC');
                },
            ]);


    }
}
