<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEventCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',entityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Ville',
            ])
            ->add('zipcode',entityType::class, [
                'class' => City::class,
                'choice_label' => 'zipcode',
                'label' => 'Code postal',
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $city = $event->getData();
            $form = $event->getForm();

            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$city || null === $city->getId()) {
                $form->add('name', TextType::class);
            }
        });
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
}
