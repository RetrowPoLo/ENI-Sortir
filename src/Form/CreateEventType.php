<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', textType::class, ['label' => 'Nom de la sortie'])
            ->add('startDateTime',textType::class, ['label' => 'Date et heure de la sortie'])
            ->add('limitDateInscription',textType::class, ['label' => 'Date limit de l\'inscription'])
            ->add('eventInfo',textType::class, ['label' => 'Nombre de place'])
            ->add('duration',textType::class, ['label' => 'Durée'])
            ->add('state',textType::class, ['label' => 'Description et infos'])
            ->add('locationSiteEvent',textType::class, ['label' => 'Nom de la sortie'])
            ->add('users',textType::class, ['label' => 'Nom de la sortie'])
            ->add('user',textType::class, ['label' => 'Nom de la sortie'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
