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

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom:',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Rajaomaniry '
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom:',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Jean de la croix'
                ]
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance: ',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lieuNaissace', TextType::class, [
                'label' => 'Lieu de naissance',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Ambila',
                    'required' => true
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse:',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Ambalakazaha Atsimo'
                ]
            ])
            ->add('cin', TextType::class, [
                'label' => 'Carte d\'identité Nationale:',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 210014708087'
                ]
            ])
            ->add('delivre', DateType::class, [
                'label' => 'Delivré le ',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fait', TextType::class, [
                'label' => 'A',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('situationMatrimoniale', ChoiceType::class, [
                'choices'  => [
                    'Célibataire' => 'Célibataire',
                    'Mariée' => 'Mariée',
                    'Veuve' => 'Veuve',
                    'Divorcée' => 'Divorcée'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: bienvenu@gmail.com'
                ]
            ])

            ->add('releveNoteBacc', CheckboxType::class, [
                'label' => 'Photocopie rélévé de note Bacc',
                'required' => false,
                'attr' => [
                    'class' => 'form-check',
                ]
            ])
            ->add('bNaissance', CheckboxType::class, [
                'label' => 'Photocopie bulettin de naissance',
                'required' => false,
                'attr' => [
                    'class' => 'form-check',
                ]
            ])
            ->add('pIdentite', CheckboxType::class, [
                'label' => 'Photocopie piece d\'idenité',
                'required' => false,
                'attr' => [
                    'class' => 'form-check',
                ]
            ])
            ->add('serie', ChoiceType::class, [
                'choices'  => [
                    'Serie A1' => 'Serie A1',
                    'Serie A2' => 'Serie A2',
                    'Serie D' => 'Serie D',
                    'Serie C' => 'Serie C',
                    'Serie OSE' => 'Serie OSE'

                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('numeroInscriptionBacc', TextType::class, [
                'label' => 'Numero d\'inscription',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 123456789'
                ]
            ])
            ->add('mention', ChoiceType::class, [

                'choices'  => [
                    'Passable' => 'Passable',
                    'Assez-bien' => 'Assez-bien',
                    'Bien' => 'Bien',
                    'Très-bien' => 'Très-bien',
                    'Excellent' => 'Excellent'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])

            ->add('centreBacc', TextType::class, [
                'label' => 'Centre d\'examen',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Manakara'
                ]
            ])
            ->add('anneeBacc', ChoiceType::class, [
                'choices'  => $this->getAnnee(2000),
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('etablissement', TextType::class, [
                'label' => 'Etablissement d\'origine',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Les capucines'
                ]
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Numero matricule ',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true
                ]
            ])
            ->add('imageFile', VichFileType::class, [
                'attr' => [
                    'required' => false
                ]
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }

    private function getAnnee($min, $max = 'current')
    {
        $annee = range($min, ($max === 'current' ? date('Y') : $max));
        return array_combine($annee, $annee);
    }
}
