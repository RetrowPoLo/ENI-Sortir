<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('username', TextType::class,  ['label' => 'Pseudo'])
//            ->add('roles')
            ->add('password',
                RepeatedType::class, [
                    'required' => false,
                    'type' => PasswordType::class,
                    'invalid_message' => 'Le mot de passe doit correspondre.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'first_options'  => ['label' => 'Mot de passe',
                        'required' => false,
                        'attr' => array(
                            'placeholder' => 'Laisser vide pour ne pas modifier le mot de passe'
                        ),
                        'empty_data' => 'noknok'],
                    'second_options' => ['label' => 'Confirmer le mot de passe',
                        'required' => false,
                        'attr' => array(
                            'placeholder' => 'Laisser vide pour ne pas modifier le mot de passe'
                        ),
                    'empty_data' => 'noknok',], ]
            )

            ->add('name', TextType::class,  ['label' => 'Nom'])
            ->add('firstName', TextType::class,  ['label' => 'Prénom'])
            ->add('phone', TelType::class,  ['label' => 'Téléphone'])
//            ->add('isActive')
//            ->add('registred')
            ->add('sites_no_site', null, ['label' => 'ville de rattachement'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
