<?php

namespace App\Form\Email;
use App\Entity\EmailTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Titre du mail',
               ])
            ->add('contentBeforeButtons',TextType::class, [
                'label' => 'Contenu avant les boutons (si il y en a)',
            ])
            ->add('contentAfterButtons',TextType::class, [
                'label' => 'Contenu après les boutons (si il y en a)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmailTemplate::class,
        ]);
    }
}