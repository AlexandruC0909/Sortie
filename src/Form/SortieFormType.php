<?php

namespace App\Form;


use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',null,
                [
                    'label'=>'Nom'
                ]
            )
            ->add('dateHeureDebut',DateType::class,
                [
                    'label'=>'dateDebut'
                ]
            )
            ->add('duree',null,
                [
                    'label'=>'duree'
                ]
            )
            ->add('dateLimiteInscription',DateType::class,
                [
                    'label'=>'dateLimite'
                ]
            )
            ->add('nbInscriptionsMax',null,
                [
                    'label'=>'nbPlaces'
                ]
            )
            ->add('infoSortie',null,
                [
                    'label'=>'description'
                ]
            )

            ->add('lieu', LieuType::class, [
                'data_class' => Lieu::class,

            ])


            ->add('enregistrer',SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
