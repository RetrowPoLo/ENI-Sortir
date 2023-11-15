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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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

            ->add('name', TextType::class, [
                'disabled' => !$isadmin,
                'label' => 'Nom'
            ])
            ->add('firstName', TextType::class, [ 'disabled' => !$isadmin,
                'label' => 'Prénom'])
            ->add('phone', TelType::class,  [
                'required'=>false,
                'label' => 'Téléphone']
                )
            ->add('plainPassword', PasswordType::class, [
                'required' => true,
                'label' => 'Mot de passe',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez mettre votre mot de passe pour valider',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer',  'attr' => ['class' => 'btn m-2 btn-outline-main'],])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
