<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\LocationSite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedLocationSite = $options['selectedLocationSite'];
        $isQuery = $options['isQuery'];
        $builder
			->add('locationSiteEvent', EntityType::class, [
				'class' => LocationSite::class,
				'choice_label' => 'name',
				'required' => false,
				'label' => false,
				'placeholder' => false,
                'data' => $selectedLocationSite,
			])
            ->add('name', TextType::class, [
				'required' => false,
				'label' => false,
				'attr' => [
					'placeholder' => 'Rechercher une sortie par mot-clé',
				],
			])
			->add('startDateTime', DateType::class, [
				'required' => false,
				'label' => 'Entre',
				'mapped' => false,
				'widget' => 'single_text',
			])
			->add('endDateTime', DateType::class, [
				'required' => false,
				'label' => 'et',
				'mapped' => false,
				'widget' => 'single_text',
			])
			->add('userIsOrganizer', CheckboxType::class, [
				'required' => false,
				'label' => 'Sorties dont je suis l\'organisateur/trice',
				'mapped' => false,
                'attr' => array('checked' => !$isQuery),
			])
			->add('userIsRegistered', CheckboxType::class, [
				'required' => false,
				'label' => 'Sorties auxquelles je suis inscrit/e',
				'mapped' => false,
                'attr' => array('checked' => !$isQuery),
			])
			->add('userIsNotRegistered', CheckboxType::class, [
				'required' => false,
				'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
				'mapped' => false,
                'attr' => array('checked' => !$isQuery),
			])
			->add('stateIsPassed', CheckboxType::class, [
				'required' => false,
				'label' => 'Sorties passées',
				'mapped' => false,
			])
			->add('save', SubmitType::class, [
				'label' => 'Recherche',
				'attr' => [
					'class' => 'btn btn-outline-primary',
				],
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
			'method' => 'GET',
			'data_class' => Event::class,
			'csrf_protection' => false,
            'selectedLocationSite' => null,
            'isQuery' => false
        ]);
    }
}
