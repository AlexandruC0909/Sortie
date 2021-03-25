<?php

namespace App\Form;


use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('dateLimiteInscription',DateTimeType::class,
                [
                    'label'=>'Date limite d\'inscription :',

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

            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'label'=>'Ville :',
                'placeholder' => 'Selectioner une ville',
                'mapped' => false,

            ])
            ->add('enregistrer',SubmitType::class)
        ;
        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event){
                $form = $event->getForm();
                $form->getParent()->add('lieu',EntityType::class,[
                   'class'=>Lieu::class,
                   'placeholder' => 'Selectioner une lieu',
                   'choices' => $form->getData()->getLieu()
                ]);

            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event){
                $form = $event->getForm();
                $data = $event->getData();
                $lieu = $data->getLieu();
                if($lieu){
                    $form->get('ville')->setData($lieu->getVille());
                    $form->add('lieu',EntityType::class,[
                        'class'=>Lieu::class,
                        'placeholder' => 'Selectioner une ville',
                        'choices' => $lieu->getVille()->getLieu()
                    ]);
                }else{
                    $form->add('lieu',EntityType::class,[
                        'class'=>Lieu::class,
                        'placeholder' => 'Selectioner une ville',
                        'choices' => []
                    ]);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
