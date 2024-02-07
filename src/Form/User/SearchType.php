<?php

namespace App\Form\User;

use App\Model\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
       $builder
           ->add('q',TextType::class);



  }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(
            ['data_calss' => SearchData::class,
                'method' => 'GET',
                'csrf_prtection' => false]

        );

  }
}