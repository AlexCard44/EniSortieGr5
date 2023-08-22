<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                "label" => "Nom de la sortie : "
            ])
            // TODO : modification format date (CSS ?)
            ->add('dateHeureDebut', null, [
                "label" => "Date et heure de début de la sortie : ",
                'widget'=>'single_text',
            ])
            ->add('dateHeureFin', null, [
                "label" => "Date et heure de fin de la sortie : ",
                'widget'=>'single_text',
            ])
            ->add('dateLimiteInscription', null, [
                "label" => "Date limite d'inscription : ",
                'widget'=>'single_text',
            ])
            ->add('nbInscriptionsMax', null, [
                "label" => "Nombre de places : "
            ])
            // TODO : input durée
            ->add('infosSortie', TextareaType::class, [
                "label" => "Description et infos : "
            ])
            // TODO : upload d'une image
//            ->add('urlPhoto', null, ["label" => "Photo : "])
            // TODO : accéder à Ville  (relation indirecte passant par Lieu et faire en sorte qu'en sélectionnant la ville, cela limite la sélection des lieux)
//            ->add('ville', EntityType::class, [
//                'class'=>Ville::class,
//                'multiple'=>false,
//                'label'=>'Ville :',
//            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'multiple' => false,
                'label' => 'Lieu : ',
            ])

            // TODO : récupérer les informations de lieu automatiquement une fois le lieu sélectionné (rue, code postal)

            // TODO : sortir 'site' du formulaire et faire en sorte de récupérer automatiquement le site relié à l'utilisateur actuellement connecté
            ->add('site', EntityType::class, [
                'class'=>Site::class,
                'multiple'=>false,
                'label'=>'Site : ',
                'choice_label' => 'nom'
            ])

            // TODO : sortir 'organisateur' du formulaire pour faire en sorte que l'organisateur soit l'utilisateur actuellement connecté
            ->add('organisateur', EntityType::class, [
                'class'=>Utilisateur::class,
                'multiple'=>false,
                'label'=>'Organisateur : ',
                'choice_label' => 'username'
            ])



            ->add('Enregistrer', SubmitType::class)// TODO : boutons publier et annuler
        ;

//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event): void {
//                $form=$event->getForm();
//
//                $data=$event->getData();
//                $lieu=$data->getLieu();
//                $rue=null===$lieu ? [] : $lieu->getRue();
//                $form->add('rue', TextareaType::class);
//            }
//        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
