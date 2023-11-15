<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FirstLoginType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('username', TextType::class, ['mapped'=>true, 'label' => 'Pseudo',
                'constraints' => [
                    new notBlank([
                        'message' => 'Donnée non valide',
                    ]),
                ],
        ])
            ->add('oldPassword' , PasswordType::class, ['mapped'=>false,
                'label' => 'Ancien mot de passe',
                'constraints' => [
                    new EqualTo([
                        'value' => 'Pa$$w0rd',
                        'message' => 'Donnée non valide',
                    ]),
                    new notBlank([
                        'message' => 'Donnée non valide',
                    ]),
                ],])
            ->add('password',
                RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Le mot de passe doit correspondre.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe',
                        'constraints' => [
                            new Regex([
                                'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9\/-])[^\/-]*$/',
                                'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spécial et ne doit pas avoir de "/" et "-"'
                            ]),
                            new length([
                                'min' => 12,
                                'minMessage' => 'Le mot de passe doit contenir au moins 12 caractères',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ])]],
                    'second_options' => ['label' => 'Confirmer le mot de passe',],
                   ]
            )


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true,
        ]);
    }
}
