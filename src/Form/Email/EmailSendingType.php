<?php

namespace App\Form\Email;

use App\Form\Email\EmailChoiceClientType;
use App\Entity\Client;
use App\Entity\EmailTemplate;
use App\Repository\ClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EmailSendingType extends AbstractType
{
    private $clientRepository;
    private $security;

    public function __construct(ClientRepository $clientRepository, Security $security)
    {
        $this->clientRepository = $clientRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emailTemplate', EntityType::class, [
                'class' => EmailTemplate::class,
                'choice_label' => 'name',
            ])

            ->add('recipient', EmailChoiceClientType::class, [
                'company_id' => $options['company_id'],
            ])
            ->add('message', TextareaType::class)
            ->add('send', SubmitType::class)
            ->add('attachments', CollectionType::class, [
                'entry_type' => VichFileType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'entry_options' => [
                    'label' => false,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'company_id' => null,
        ]);
    }
}