<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                    'label'=>'Latitute :'
                ]
            )
            ->add('longitude',null,
                [
                    'label'=>'Longitude :'
                ]
            )

            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom'

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
