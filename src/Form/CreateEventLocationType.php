<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEventLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedLocation = $options['selectedLocation'];
        $selectedCity = $options['selectedCity'];
        $builder
            ->add('city', CreateEventCityType::class, [
                'label' => false,
                'selectedCity' => $selectedCity,
            ])
            ->add('name', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'label' => 'Lieu de la sortie',
                'required' => true,
                'data' => $selectedLocation
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
            'selectedCity' => null,
            'selectedLocation' => null,
        ]);
    }
}