<?php

namespace App\Form;


use App\Entity\Lieu;
use App\Entity\Sortie;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('dateHeureDebut',DateTimeType::class,
                [
                    'label'=>'dateDebut',
                    'attr' => ['class' => 'dateFormulaire'],
                    'widget' => 'single_text',
                    'required' => true,
                ]
            )
            ->add('duree',\Symfony\Component\Form\Extension\Core\Type\IntegerType::class,
                [
                    'label'=>'duree',
                    'attr' => ['min' => 1]
                ]
            )
            ->add('dateLimiteInscription',DateType::class,
                [
                    'label'=>'dateLimite',
                    'attr' => ['class' => 'dateFormulaire'],
                    'widget' => 'single_text',
                    'required' => true,
                ]
            )
            ->add('nbInscriptionsMax',\Symfony\Component\Form\Extension\Core\Type\IntegerType::class,
                [
                    'label'=>'nbPlaces',
                    'attr' => ['min' => 1]
                ]
            )
            ->add('infoSortie',TextareaType::class,
                [
                    'label'=>'description'
                ]
            )

            ->add('lieu', LieuType::class, [
                'data_class' => Lieu::class,
                'label'=>'Lieu'

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
