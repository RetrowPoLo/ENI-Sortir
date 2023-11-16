<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\LocationSite;
use App\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFilterAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedLocationSite = $options['selectedLocationSite'];
        $builder
			->add('locationSiteEvent', EntityType::class, [
				'class' => LocationSite::class,
				'choice_label' => 'name',
				'required' => false,
				'label' => false,
				'placeholder' => 'Sélectionner le site',
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
			->add('state', EnumType::class, [
				'class' => State::class,
				'required' => false,
				'label' => false,
				'choice_label' => fn ($choice) => match ($choice) {
					State::Created => 'En création',
					State::Open => 'Ouvert',
					State::Closed  => 'Clôturé',
					State::InProgress  => 'En cours',
					State::Passed  => 'Terminé',
					State::Canceled  => 'Annulé',
				},
				'placeholder' => 'Sélectionner l\'état',
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
            'selectedLocationSite' => null
        ]);
    }
}
