<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\LocationSite;
use App\Entity\User;
use App\Repository\CityRepository;
use ContainerMMVaBo8\getLocationSiteRepositoryService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Length;

class EditUserType extends AbstractType
{
    private $authChecker;

    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isadmin = $this->authChecker->isGranted('ROLE_ADMIN');
        $builder
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('username', TextType::class, ['label' => 'Pseudo',])
            ->add('password',
             RepeatedType::class, [
        'type' => PasswordType::class,
        'invalid_message' => 'Le mot de passe doit correspondre.',
        'options' => ['attr' => ['class' => 'password-field']],
        'required' => true,
        'first_options'  => ['label' => 'Mot de passe',
            'constraints' => [
                new length([
                    'min' => 12,
                    'minMessage' => 'Le mot de passe doit contenir au moins 12 caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ])]],
        'second_options' => ['label' => 'Confirmer le mot de passe'],
                    'constraints' => [
                        new length([
                            'min' => 12,
                            'minMessage' => 'Le mot de passe doit contenir au moins 12 caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ])]],
            )

            ->add('name', TextType::class, [
                'disabled' => !$isadmin,
                'label' => 'Nom'
            ])
            ->add('firstName', TextType::class, [ 'disabled' => !$isadmin,
                'label' => 'Prénom'])
            ->add('phone', TelType::class,  ['label' => 'Téléphone'])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
