<?php

namespace App\Controller\Admin;

use App\Entity\Site;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(true),
            TextField::new('username'),
            ArrayField::new('roles'),
            TextField::new('password'),#->setFormType(PasswordType::class),
            TextField::new('nom'),
            TextField::new('prenom'),
            TelephoneField::new('telephone'),
            EmailField::new('mail'),
            BooleanField::new('administrateur'),
            BooleanField::new('actif'),
            AssociationField::new('sortiesOrganisees')->autocomplete(),
            AssociationField::new('sortiesParticipees')->autocomplete(),
            ImageField::new('photo')->setUploadDir('public/images/'),
            AssociationField::new('site')->autocomplete()
        ];
    }

}
