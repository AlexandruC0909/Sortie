<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            ->add('site', EntityType::class, [
             'class' => Site::class,
             'choice_label' => 'nom',
         ])
             ->add('plainPassword', PasswordType::class, [
             // instead of being set onto the object directly,
             // this is read and encoded in the controller

             'mapped' => false,
             'constraints' => [

                 new Length([
                     'min' => 4,
                     'minMessage' => 'Your password should be at least {{ limit }} characters',
                     // max length allowed by Symfony for security reasons
                     'max' => 4096,
                 ]),
             ],
                 'required'=> false
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
