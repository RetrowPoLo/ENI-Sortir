<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\LocationSite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', textType::class, ['label' => 'Nom de la sortie'])
            ->add('startDateTime',DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début de la sortie',
            ])
            ->add('endDateTime',DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin de la sortie',
            ])
            ->add('limitDateInscription',DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription',
            ])
            ->add('nbInscriptionMax',IntegerType::class, ['label' => 'Nombre de place', 'attr' => [
                'min' => 1
            ]])
            ->add('event_info',textareaType::class,[
                'label' => 'Description et infos',
                'required' => false,
            ])
            ->add('locationSiteEvent', EntityType::class, [
                'class' => LocationSite::class,
                'choice_label' => 'name',
                'label' => 'Site',
                'required' => true,
            ])
            ->add('eventLocation', CreateEventLocationType::class, [
                'label' => false,
            ])

            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publish', SubmitType::class, ['label' => 'Publier'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
