<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, ["label"=> "Nom de la sortie : "])
            // TODO : modification format date (CSS ?)
            ->add('dateHeureDebut', null, ["label" => "Date et heure de la sortie : "])
            ->add('dateLimiteInscription', null, ["label" => "Date limite d'inscription : "])
            ->add('nbInscriptionsMax', null, ["label" => "Nombre de places : "])
            // TODO : input durée
            ->add('infosSortie', TextareaType::class,["label" => "Description et infos : "] )
            // TODO : upload d'une image
//            ->add('urlPhoto', null, ["label" => "Photo : "])
            // TODO : accéder à Ville  (relation indirecte passant par Lieu)
//            ->add('ville', EntityType::class, [
//                'class'=>Ville::class,
//                'multiple'=>false,
//                'label'=>'Ville :',
//            ])
            ->add('lieu', EntityType::class, [
                'class'=> Lieu::class,
                'multiple'=> false,
                'label'=> 'Lieu : ',
            ])
            // TODO : récupérer les informations de lieu (rue, code postal)

            ->add('Enregistrer', SubmitType::class)
            // TODO : boutons publier et annuler
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
