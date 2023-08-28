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
        // $user = $options['user'];
        $builder
            ->add('username')
            //->add('roles')
//            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
//            ->add('administrateur')
//            ->add('actif')
//            ->add('site', EntityType::class, [
//                'class' => Site::class,
//                'choice_label' => 'nom'
//            ])
//            ->add('sortiesParticipees', CollectionType::class,[
//                'entry_type'=> SortieType::class,
//                'allow_add' => false,
//                'allow_delete' => false,
//                'by_reference' => false,
//                //'disabled' => true,
//             //   'query_builder' =>
//
//            ])
//            ->add('photo', FileType::class,[
//                'label'=>'Ajouter une image de profil ',
//                'required'=>false,
//                'constraints'=> [
//                    new File([
//                        'maxSize'=> '4M',
//                        'mimeTypes'=>[
//                            'image/jpeg',
//                            'image/png'
//                        ],
//                        'mimeTypesMessage'=> 'Veuillez télécherger uune image au format JPEG ou PNG'
//                    ])
//                ]
//            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage'=> 'Veuillez télécharger une image au format JPEG ou PNG'
                    ])
                ]
            ])
            ->add('modifier', SubmitType::class);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
