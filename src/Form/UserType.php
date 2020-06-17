<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username')
        ->add('email', EmailType::class)
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
                'SuperAdmin' => "ROLE_SUPER_ADMIN",
            ],
            'multiple' => true,
            'expanded' => true,
        ])
        ->add('avatar')
        ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $user = $event->getData();

            // On veut modifier le champs password pour qu'il soit requis si notre utilisateur est tout neuf
            if ($user->getId() === null) {
                // Pour changer una valeur dans les options d'un champs,
                // il faut supprimer le champs de $form et l'ajouter à nouveau avec des options différentes
                $form
                    ->remove('password')
                    ->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                        'first_options'  => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Retapez votre mot de passe'],
                        'mapped' => false,
                        'required' => true,
                    ])
                    ->add('agreeTerms', CheckboxType::class, [
                        'mapped' => false,
                        'label' => 'J\'accepte les CGU',
                    ])
                ;
            }
        })
    ;
    }
    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
