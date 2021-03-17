<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            ->add('update',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
