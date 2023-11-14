<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword' , PasswordType::class, ['mapped'=>false,
                'label' => 'Ancien mot de passe',
                'constraints' => [
                    new notBlank([
                        'message' => 'Veuillez saisir l\'ancien mot de passe',
                    ]),
                ],])
            ->add('password',
             RepeatedType::class, [
                    'mapped'=>false,
        'type' => PasswordType::class,
        'invalid_message' => 'Le mot de passe doit correspondre.',
        'options' => ['attr' => ['class' => 'password-field']],
        'required' => true,
        'first_options'  => ['label' => 'Mot de passe',
            'constraints' => [
                new Regex([
                    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\/-])[^\/-]*$/',
                    'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et ne doit pas avoir de "/" et "-"'
                ]),
                new length([
                    'min' => 12,
                    'minMessage' => 'Le mot de passe doit contenir au moins 12 caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ])]],
        'second_options' => ['label' => 'Confirmer le mot de passe'],
],
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
