<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MotDePasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class,[
                'label' => 'Ancien mot de passe',

            ])
            ->add('newPassword', RepeatedType::class,[
                'type'=>PasswordType::class,
                'first_options' => [
                    'label'=>'Nouveau mot de passe',
                    'attr' =>['id' => 'newPassword']
                ],
                'second_options' => [
                    'label'=>'Confirmer nouveau mot de passe',
                    'attr' =>['id' => 'confirmNewPassword']
                ],
                "mapped"=> false,


            ])
            ->add('modifier', SubmitType::class);



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
//            'data_class' => Utilisateur::class,
        ]);
    }
}
