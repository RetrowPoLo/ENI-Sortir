<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {$builder
        ->add('pictureUpload', FileType::class, [
            'label' => 'Photo',
            'attr' => ['placeholder' => 'Veuillez choisir un fichier...']
        ])
        ->add('submit', SubmitType::class, ['label' => 'Envoyer !',
            'attr' => ['class' => 'btn m-2 btn-outline-main btn']]);
//        ->add('cancel', ButtonType::class, [
//    'attr' => ['class' => 'btn btn-outline-darklight',
//        'href' => ],
//]);
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
