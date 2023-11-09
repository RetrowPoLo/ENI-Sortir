<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
				'required' => false,
				'label' => false,
				'attr' => [
					'placeholder' => 'Rechercher une sortie par mot-clé',
				],
			])
			->add('startDateTime', DateType::class, [
				'required' => false,
				'label' => false,
				'attr' => [
					'placeholder' => 'Entre',
				],
			])
			->add('endDateTime', DateType::class, [
				'required' => false,
				'label' => false,
				'attr' => [
					'placeholder' => 'et',
				],
			])
			->add('save', SubmitType::class, [
				'label' => 'Recherche',
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
			'method' => 'GET',
            'data_class' => Event::class,
			'csrf_protection' => false,
        ]);
    }
}
