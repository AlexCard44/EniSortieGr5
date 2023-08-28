<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SortieType extends AbstractType
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack) {
        $this->requestStack=$requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUrl=$this->requestStack->getCurrentRequest()->getPathInfo();
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
            ->add('lieu', EntityType::class, [
                'class'=> Lieu::class,
                'multiple'=>false,
                'label'=>'Ville :',
                'choice_label' => 'rue',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'multiple' => false,
                'label' => 'Lieu : ',
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage'=> 'Veuillez télécharger une image au format JPEG ou PNG',
                    ])
                ]
            ])

            // TODO : récupérer les informations de lieu automatiquement une fois le lieu sélectionné (rue, code postal)


            ->add('Enregistrer', SubmitType::class);

//            if(str_contains($currentUrl,'creation')) {
//                $builder->add('Publier', SubmitType::class, [
//                    "label" => "Publier la sortie"
//                ]);
//            }

            if(str_contains($currentUrl,'edit')) {
                $builder->add('Annuler', SubmitType::class, [
                    "label" => "Annuler la sortie"
                ]);
            }

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $sortie=$event->getData();
                $form=$event->getForm();
                if ($sortie && !$sortie->isEstPublie()) {
                    $form->add('Publier', SubmitType::class);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
