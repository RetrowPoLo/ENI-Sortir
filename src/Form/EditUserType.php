<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\LocationSite;
use App\Entity\User;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditUserType extends AbstractType
{
//    user@user.com
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
//            ->add('roles')
            ->add('password',
             RepeatedType::class, [
        'type' => PasswordType::class,
        'invalid_message' => 'Le mot de passe doit correspondre.',
        'options' => ['attr' => ['class' => 'password-field']],
        'required' => true,
        'first_options'  => ['label' => 'Password'],
        'second_options' => ['label' => 'Repeat Password'], ]
            )

            ->add('name', TextType::class, [ 'disabled' => true ])
            ->add('firstName', TextType::class)
            ->add('phone', TelType::class)
            ->add('sites',  EntityType::class, [
                'mapped' => false,
                'class' => LocationSite::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                },])
//                'choice_value' => function (?LocationSite $entity) {
//                    return $entity ? $entity->getId() : '';
//                },])
//            ->add('isActive')
//            ->add('registred')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
