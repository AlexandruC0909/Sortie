<?php

namespace App\Form;

use App\Entity\Participant;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('pseudo')
             ->add('password', RepeatedType::class, [
                 'type' => PasswordType::class,
                 'invalid_message' => 'Les mots de passe doivent correspondre',
                 'options' => ['attr' => ['class' => 'password-field']],
                 'first_options'  => ['label' => 'nouveau mot de passe'],
                 'second_options' => ['label' => 'confirmer'],
                 'mapped' =>false,
                 'required' => false

             ])
            /* Partie du formulaire pour Uploader une photo*/
            ->add('imageFile', FileType::class, [
                'required' => false,
            ])
            ->add('Enregistrer',SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-2'
                ]
            ])
            ->add('Annuler', ResetType::class, [
                'attr' => [
                    'class' => 'btn btn-danger mt-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
