<?php

namespace App\Form;

use App\Entity\LocationSite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LocationSiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
				'required' => false,
				'label' => false
			])
            ->add('save', SubmitType::class, [
				'label' => 'Rechercher',
				'attr' => [
					'class' => 'btn btn-outline-primary px-3 mb-3'
				]
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LocationSite::class,
        ]);
    }
}
