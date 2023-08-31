<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Repository\SortieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MonProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, [
                'label' => 'Pseudo'
            ])
            ->add('nom')
            ->add('prenom', null, [
                'label' => 'Prénom'
            ])
            ->add('telephone', null, [
                'label' => 'Téléphone'
            ])
            ->add('mail')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Photo',
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG'
                    ])
                ],
                'download_uri' => false,
            ])
            ->add('modifier', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-lime-500 hover:bg-lime-700 text-white font-bold py-2 px-4 border border-lime-700 rounded'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
