<?php

namespace App\Form;

use App\Entity\Frais;
use App\Entity\Niveau;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ecolage', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Ar 100 000'
                ]
            ])
            ->add('droit', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Ar 100 000'
                ]
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'attr' => [
                    'class' => 'form-control',
                    'required' => true,
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->where('n.statut = :statut')
                        ->setParameter('statut', false);
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Frais::class,
        ]);
    }
}
