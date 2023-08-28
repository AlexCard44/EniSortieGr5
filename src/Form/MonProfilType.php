<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Repository\SortieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('modifier', SubmitType::class,[
                'attr' => [
                    'class' => 'bg-lime-500 hover:bg-lime-700 text-white font-bold py-2 px-4 border border-lime-700 rounded'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
