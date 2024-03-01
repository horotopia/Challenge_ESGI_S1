<?php

namespace App\Form\Invoice;
use App\Entity\Quote;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;




class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {  $companyId= $options['companyId'];
        $today = new \DateTime();
           $builder
           ->add('quote', TextType::class, [
            'label' => 'Devis N°:',
            'disabled' => false,
            'attr' => [
                'readonly' => true,
            ],
             ])
            ->add('client', TextType::class, [
                'label' => 'Client',
                'disabled' => false,
                'attr' => [
                    'readonly' => true, // Définir les champs comme en lecture seule
                ],
            ])
            ->add('totalTTC', NumberType::class, [
                'label' => 'Montant',
                'required' => true,
                'disabled' => false,
                'attr' => [
                    'readonly' => true, // Définir les champs comme en lecture seule
                ],
            ])
            ->add('IdClient' ,IntegerType::class, [
                'label_attr' => [
                    'class' => '',
                ],
                'attr' => [
                    'class' => '',
                ],
            ])
            ->add('IdDevis' ,IntegerType::class, [
                'label_attr' => [
                    'class' => '',
                ],
                'attr' => [
                    'class' => '',
                ],
            ])
            ->add('dueDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de paiement',
                'data' => $today, 
                
            ])
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => [
                    'Espéces' => 'Especes',
                    'Carte bancaire' => 'Carte',
                    'Chéque Bancaire' => 'Cheque',
                ],
                'label' => 'Méthode de paiement',
            ])
            ->add('Enregistrer',SubmitType::class)

        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'companyId'=>null,
            'amount' => null,
        ]);
    }
}



