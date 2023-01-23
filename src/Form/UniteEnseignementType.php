<?php

namespace App\Form;

use App\Entity\UniteEnseignement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UniteEnseignementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Physiologie, Pharmacologie,...'
                ]
            ])
            ->add('credit', IntegerType::class, [
                'label' => 'Crédit',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('niveau', null, [
                'label' => 'Niveau',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('professeur', null, [
                'label' => 'Professeur',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('semestre', null, [
                'label' => 'Semestre inclues',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UniteEnseignement::class,
        ]);
    }
}
