<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{


    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentURL = $this->requestStack->getCurrentRequest()->getPathInfo();
        $builder
            ->add('nom', null, [
                "label" => "Nom du lieu : "
            ])
            ->add('rue', null, [
                "label" => "Numéro et nom de la rue"
            ])
            ->add('latitude', null, [
                "label" => "Latitude (coordonnées GPS)"
            ])
            ->add('longitude', null, [
                "label" => "Longitude (coordonnées GPS)"
            ])
            ->add('ville', EntityType::class, [
                'class'=>Ville::class,
                'required' => true,
                'multiple' => false,
                'label' => 'Ville',
                'choice_label' => 'nom'
            ])
            ->add('Enregistrer', SubmitType::class);

        if (str_contains($currentURL, 'edit')) {
            $builder
                ->add('Supprimer', SubmitType::class, [
                    "label" => "Supprimer le lieu"
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
