<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEventLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', CreateEventCityType::class, [
                'label' => false,
            ])
            ->add('name', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'label' => 'Lieu de la sortie',
                'required' => true,
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'required' => false,
                'disabled' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'selected_city' => null,
            'selected_location' => null,
        ]);
    }
}