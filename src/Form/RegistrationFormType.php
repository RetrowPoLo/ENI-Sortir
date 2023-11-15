<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
				'required' => true,
				'label' => 'Adresse email*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Erreur de données',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'L\'adresse email doit contenir au moins {{ limit }} caractères !',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
			])
//			->add('username', TextType::class, [
//				'required' => true,
//				'label' => 'Nom d\'utilisateur'
//			])
			->add('name', TextType::class, [
				'label' => 'Nom*'
			])
			->add('firstName', TextType::class, [
				'label' => 'Prénom*'
			])
			->add('phone', TelType::class, [
				'required' => false,
				'label' => 'Téléphone'

			])
//            ->add('plainPassword', PasswordType::class, [
//				'required' => true,
//                // instead of being set onto the object directly,
//                // this is read and encoded in the controller
//                'mapped' => false,
//                'attr' => ['autocomplete' => 'new-password'],
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Veuillez saisir un mot de passe !',
//                    ]),
//                    new Length([
//                        'min' => 6,
//                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères !',
//                        // max length allowed by Symfony for security reasons
//                        'max' => 4096,
//                    ]),
//                ],
//				'label' => 'Mot de passe'
//            ])
                ->add('sites_no_site', null, [
                    'label' => 'Site*'
                ])
			->add('isActive', CheckboxType::class, [
				'required' => false,
				'label' => 'Activer le compte ?',
			])
			->add('save', SubmitType::class, [
				'label' => 'Enregistrer',
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
