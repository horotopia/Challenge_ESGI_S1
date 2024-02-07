<?php

namespace App\Form\facture;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('devis', EntityType::class, [
                'class' => Devis::class,
                'choice_label' => function (Devis $devis) {
                    return sprintf('Devis %s - %s', $devis->getNumDevis(), $devis->getIdClient()->getNom());
                },
                'placeholder' => 'Sélectionnez un devis',
                'label' => 'Devis',
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
                'required' => false,
            ])
            ->add('dateEcheance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'échéance',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'Payée' => 'payee',
                    'Annulée' => 'annulee',
                ],
                'label' => 'Statut',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configuration des options par défaut ici
        ]);
    }
}
