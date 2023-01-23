<?php

namespace App\Form;

use App\Form\NoteType;
use App\Entity\Resultat;
use App\Entity\Semestre;
use App\Entity\UniteEnseignement;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ResultatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('semestre', EntityType::class, [
            //     'class' => Semestre::class,
            //     'label' => 'Semestre',
            //     'attr' => [
            //         'class' => 'form-control'
            //     ],               
            //     'query_builder' => function (EntityRepository $er) {
            //         return $er->createQueryBuilder('s')
            //             ->where('s.statut = :statut')
            //             ->setParameter('statut', true);
            //     }
            // ])
            // ->add('examen', null, [
            //     'label' => 'Examen',
            //     'attr' => [
            //         'class' => 'form-control'
            //     ]
            // ])
            ->add('uniteEnseignement', EntityType::class, [
                'class' => UniteEnseignement::class,
                'label' => ' ',
                'attr' => [
                    'class' => 'form-control'
                ],               
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.statut = :statut')
                        ->setParameter('statut', true);
                }
            ])
            ->add('notes', CollectionType::class, [
                'label' => ' ',
                'entry_type' => NoteType::class,
                'allow_add' => true
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resultat::class,
            'allow_extra_fields' => true,
        ]);
    }
}
