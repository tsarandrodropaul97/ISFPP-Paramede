<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Niveau;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class EtudiantReinscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom:',
                'attr' => [
                    'class' => 'form-control',
                    
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom:',
                'attr' => [
                    'class' => 'form-control',
                    
                ]
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Numero matricule: ',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Les capucines',
                    'readonly' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
