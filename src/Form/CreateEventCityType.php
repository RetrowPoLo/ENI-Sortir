<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEventCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville',
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal',
                'mapped' => false,
                'disabled' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
            'selected_city' => null,
        ]);
    }
}
