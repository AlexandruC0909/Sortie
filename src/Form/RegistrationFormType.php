<?php

    namespace App\Form;


    use App\Entity\Participant;
    use App\Entity\Site;
    use phpDocumentor\Reflection\Types\Array_;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\IsTrue;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;

    class RegistrationFormType extends AbstractType
    {

        public function buildForm(FormBuilderInterface $builder, array $options)
        {


            $builder
                ->add('email')
                ->add('pseudo', TextType::class,
                    [
                        'label' => 'Pseudo',
                    ])
                ->add('nom', TextType::class,
                    [
                        'label' => 'Nom',
                    ])
                ->add('prenom', TextType::class,
                    [
                        'label' => 'Prénom',
                    ])
                ->add('telephone', null,
                    [
                        'label' => 'Numéro de téléphone (facultatif)',
                    ])
                ->add('site', EntityType::class,
                    [
                        'class' => Site::class,
                        'choice_label' => 'nom',
                        'label' => 'Veuillez sélectionner votre centre ENI',
                    ])
                ->add('administrator', null,
                    [
                        'label' => 'Administrateur',
                    ])

                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'options' => ['attr' => ['class' => 'password-field']],
                    'invalid_message' => 'Les deux mots de passes doivent correspondre.',
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe (Minimum 6 caractères)'],
                    'second_options' => ['label' => 'Repetez mot de passe'],
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez votre mot de passe',
                        ]),

                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir {{ limit }} caractères minimum',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ]);
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Participant::class,
            ]);
        }
    }
