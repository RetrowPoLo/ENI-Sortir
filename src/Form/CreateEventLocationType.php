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
    private $selectedCity;
    private $selectedLocation;

    public function __construct(CityRepository $cityRepository, LocationRepository $locationRepository)
    {
        $city = $cityRepository->findOneBy([], ['id' => 'ASC']);
        $location = new Location();
        if($city != null){
            $location = $locationRepository->findOneBy(['city' => $city], ['id' => 'ASC']);
        }
        $this->selectedLocation = $location;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedCity = $options['selected_city'];
        $selectedLocation = $options['selected_location'];

        if($selectedLocation != null){
            $this->selectedLocation = $selectedLocation;
        }

        $builder
            ->add('city', CreateEventCityType::class, [
                'label' => false,
                'selected_city' => $selectedCity,
            ])
            ->add('name', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'label' => 'Lieu de la sortie',
                'required' => true,
                'data' => $this->selectedLocation,
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'required' => false,
                'disabled' => true,
                'data' => $this->selectedLocation ? $this->selectedLocation->getStreet() : ""
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