<?php

namespace App\Form\Invoice;

use App\Entity\Quote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quotes', EntityType::class, [
                'class' => Quote::class,
                'choice_label' => function (Quote $devis) {
                    return sprintf('Quote %s - %s', $devis->getQuotationNumber(), $devis->getClientId()->getLastName());
                },
                'placeholder' => 'Sélectionnez un quotes',
                'label' => 'Quote',
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
