<?php

namespace App\Form\Email;

use App\Entity\EmailTemplate;
use App\Repository\ClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'label' => 'Structure de l\'email',
                'class' => EmailTemplate::class,
                'choice_label' => 'name',
            ])

            ->add('recipient', EmailChoiceClientType::class, [
                'label' => 'Envoyer à',
                'company_id' => $options['company_id'],
            ])
            ->add('message', TextareaType::class)
            ->add('attachments', FileType::class, [
                'label' => 'Pièces jointes',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'accept' => 'image/*, application/pdf',
                ],
            ])
            ->add('send', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'company_id' => null,
        ]);
    }
}