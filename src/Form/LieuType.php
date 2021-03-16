<?php

namespace App\Form;

use App\Entity\Lieu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',null,
                [
                    'label'=>'nom'
                ]
            )
            ->add('rue',null,
                [
                    'label'=>'rue'
                ]
            )
            ->add('latitude',null,
                [
                    'label'=>'latitute'
                ]
            )
            ->add('longitude',null,
                [
                    'label'=>'longitude'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
