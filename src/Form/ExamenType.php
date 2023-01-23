<?php

namespace App\Form;

use App\Entity\Examen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'examen',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Examen PS 2023'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Première session' =>'Première session' ,
                    'Deuxième session' => 'Deuxième session'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('anneeUniversitaire', null, [
                'label' => "Année universitaire",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Examen::class,
        ]);
    }
}
