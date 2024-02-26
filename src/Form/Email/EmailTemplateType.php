<?php

namespace App\Form\Email;
use App\Entity\EmailTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de modèle',
                'placeholder' => '-- Sélectionnez un type --',
                'choices' => [
                    'Devis' => EmailTemplate::TYPE_QUOTE,
                    'Facture' => EmailTemplate::TYPE_INVOICE,
                    'Autre' => EmailTemplate::TYPE_OTHER,
                ],
                'required' => true,
            ])
            ->add('name',TextType::class, [
                'label' => 'Titre du mail',
            ])
            ->add('contentBeforeButtons',TextareaType::class, [
                'label' => 'Contenu avant les boutons (si il y en a)',
                'attr' => ['rows' => '9'],
            ])
            ->add('contentAfterButtons',TextareaType::class, [
                'label' => 'Contenu après les boutons (si il y en a)',
                'attr' => ['rows' => '9'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmailTemplate::class,
        ]);
    }
}