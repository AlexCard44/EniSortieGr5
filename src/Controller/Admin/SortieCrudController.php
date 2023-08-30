<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(true),
            TextField::new('nom'),
            DateTimeField::new('dateHeureDebut'),
            DateTimeField::new('dateHeureFin'),
            DateTimeField::new('dateLimiteInscription'),
            IntegerField::new('nbInscriptionsMax'),
            TextField::new('infosSortie'),
            ImageField::new('urlPhoto')->setUploadDir('public/images/'),
            BooleanField::new('estPublie'),
            AssociationField::new('lieu')->autocomplete(),
            AssociationField::new('etat'),
            AssociationField::new('site')->autocomplete(),
            AssociationField::new('organisateur')->autocomplete(),
            AssociationField::new('participants')->autocomplete(),
        ];
    }

}
