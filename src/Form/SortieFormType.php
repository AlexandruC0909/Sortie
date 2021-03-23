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
                    'label'=>'Nom de la sortie :'
                ]
            )
            ->add('dateHeureDebut',DateTimeType::class,
                [
                    'label'=>'Date et heure de la sortie :',
                    'attr' => ['class' => 'dateFormulaire'],
                    'widget' => 'single_text',
                    'required' => true,
                ]
            )
            ->add('duree',\Symfony\Component\Form\Extension\Core\Type\IntegerType::class,
                [
                    'label'=>'Durée :',
                    'attr' => ['min' => 20]
                ]
            )
            ->add('dateLimiteInscription',DateType::class,
                [
                    'label'=>'Date limite d\'inscription :',
                    'attr' => ['class' => 'dateFormulaire'],
                    'widget' => 'single_text',
                    'required' => true,
                ]
            )
            ->add('nbInscriptionsMax',\Symfony\Component\Form\Extension\Core\Type\IntegerType::class,
                [
                    'label'=>'Nombre de places :',
                    'attr' => ['min' => 1]
                ]
            )
            ->add('infoSortie',TextareaType::class,
                [
                    'label'=>'Description et infos :'
                ]
            )

            ->add('lieu', LieuType::class, [
                'data_class' => Lieu::class,
                'label'=>'Lieu :'

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
