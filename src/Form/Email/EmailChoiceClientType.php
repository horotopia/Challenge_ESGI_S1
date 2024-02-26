<?php

namespace App\Form\Email;

use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class EmailChoiceClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('select', ChoiceType::class, [
                'choices' => [
                    'Choisir un client existant' => 'existing',
                    'Saisir une adresse e-mail' => 'manual',
                ],
                'label' => 'À',
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'attr' => ['class' => 'flex flex-row w-full p-2 mb-3 mr-2 outline-none'],
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getFirstName() . ' ' . $client->getLastName() . ' (' . $client->getEmail() . ')';
                },
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.companyId = :companyId')
                        ->setParameter('companyId', $options['company_id']);
                },
                'required' => false,
                'placeholder' => 'Sélectionnez un client',
                'attr' => ['class' => 'flex flex-col w-full mt-3 p-2 mb-3 mr-2 rounded border-2 border-solid border-[#A0A0A0] outline-none'],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'attr' => ['class' => 'flex flex-col w-full mt-3 p-2 mb-3 mr-2 rounded border-2 border-solid border-[#A0A0A0] outline-none'],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (null === $data) {
                    return;
                }

                if (empty($data->getClient()) && empty($data->getEmail())) {
                    $form->get('select')->setData('manual');
                } else {
                    $form->get('select')->setData('existing');
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if ('manual' === $data['select']) {
                    unset($data['client']);
                } else {
                    unset($data['email']);
                }
                $event->setData($data);
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'company_id' => null,
        ]);
    }
}