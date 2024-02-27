<?php

namespace App\Form\Invoice;

use App\Entity\Quote;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $companyId = $options['companyId'];

        $builder
            ->add('quotes', EntityType::class, [
                'class' => Quote::class,
                'choice_label' => function (Quote $devis) {
                    return sprintf('%s - %s', $devis->getQuotationNumber(), $devis->getClientId()->getLastName());
                },
                'placeholder' => 'Sélectionnez un devis',
                'label' => 'Quote',
                'query_builder' => function (EntityRepository $entityRepository) use ($companyId) {
                    return $entityRepository->createQueryBuilder('q')
                        ->join('q.clientId', 'c')
                        ->andWhere('c.companyId = :companyId')
                        ->andWhere('q.status = :status')
                        ->setParameter('companyId', $companyId)
                        ->setParameter('status', 'Accepté');
                },
            ])
            ->add('client', TextType::class, [
                'label' => 'Client',
                'disabled' => true,
            ])
            ->add('totalTTC', NumberType::class, [
                'label' => 'Montant',
                'required' => true,
                'disabled' => true,
            ])
            ->add('dueDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'échéance',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'En attente',
                ],
                'label' => 'Statut',
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => [
                    'Carte bancaire' => 'Carte',
                    'Espéces' => 'Especes',
                ],
                'label' => 'Méthode de paiement',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'companyId' => null,
            'amount' => null,
        ]);
    }
}






