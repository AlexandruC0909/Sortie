<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',SearchType::class,
                [
                    'label' =>'nom',
                    'attr'=>[
                        'placeholder' => 'nom de sortie'
                    ],
                    'required' => false
                ]
            )

            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'required' => false

            ]
            )
            ->add('Rechercher',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
