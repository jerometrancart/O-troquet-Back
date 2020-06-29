<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username')
            ->add('email', EmailType::class)

            ->add('roles', ChoiceType::class
                , [
                    'choices' => [
                        'Utilisateur' => 'ROLE_USER',
                        'Administrateur' => 'ROLE_ADMIN',
                        'SuperAdmin' => "ROLE_SUPER_ADMIN",

                    ],
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('avatar')
            /* ->add('avatar', FileType::class, [
                'label' => 'avatar',
                'mapped' => true,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '536k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/x-jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ]) */
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email,
                    new NotBlank,
                ],
            ])
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 16,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
