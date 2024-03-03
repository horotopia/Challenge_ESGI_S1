<?php

namespace App\Form\Contact;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', TextType::class, ['label' => 'Company'])
            ->add('fullName', TextType::class, ['label' => 'Full Name'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('phone', TextType::class, ['label' => 'Phone'])
            ->add('subject', TextType::class, ['label' => 'Subject of Your Inquiry'])
            ->add('message', TextareaType::class, ['label' => 'Message']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}