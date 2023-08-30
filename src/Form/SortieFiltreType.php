<?php

namespace App\Form;

use App\Entity\SortiesFiltre;
use App\Entity\Utilisateur;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => ['placeholder'=>'Rechercher'],
                'label' =>false,
                'required' => false
            ])
            ->add('sortiesOrganisees',CheckboxType::class,[
                'label'=>'Sorties que j\'organise',
                'required'=>false
            ])
            ->add('sortiesInscrit',CheckboxType::class,[
                'label'=>'Sorties auxquelles je suis inscrit',
                'required'=>false
            ])
            ->add('sortiesNonInscrit',CheckboxType::class,[
                'label'=>'Sorties auxquelles je ne participe pas',
                'required'=>false
            ])
            ->add('sortiesPassees',CheckboxType::class,[
                'label'=>'Sorties passées',
                'required'=>false
            ])
            ->add('dateTime', DateTimeType::class,[
                'label'=>'Date de début',
                'required'=> false,
                'widget'=>'single_text'
            ])

            ->add('Rechercher', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-lime-500 hover:bg-lime-700 text-white font-bold py-2 px-4 border border-lime-700 rounded ml-4'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SortiesFiltre::class,
        ]);
    }


}
