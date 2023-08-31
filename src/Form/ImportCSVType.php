<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImportCSVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('csvFile', FileType::class, [
                'label' => 'Fichier CSV',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => 'fichier/csv',
                        'mimeTypesMessage'=>'Veuillez téléverser un fichier au format CSV'
                    ])
                ],
                'attr' => [
                    'accept' => '.csv', // Limitez le type de fichier à .csv
                ],
            ])
            ->add('import', SubmitType::class, [
                'label' => 'Importer',
                'attr' => [
                    'class' => 'bg-lime-500 hover:bg-lime-700 text-white font-bold py-2 px-4 border border-lime-700 rounded'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
