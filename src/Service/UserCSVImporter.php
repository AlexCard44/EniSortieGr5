<?php

namespace App\Service;


use App\Entity\Utilisateur;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCSVImporter
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importCSV(UploadedFile $file,
                              SiteRepository $siteRepository,
                              UserPasswordHasherInterface $userPasswordHasher)
    {

        $reader = new Csv();
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();

        $csvData = array_map('str_getcsv', file($file->getPathname()));
        $it = $worksheet->getRowIterator()->seek(1);

        foreach ($it as $row) {
            $cellule = $row->getCellIterator();
            $user = new Utilisateur();
            $tab = [];
            foreach ($cellule as $cell) {
                  $tab[] = $cell->getFormattedValue();
            }
            //dd($tab);
            $user->setUsername($tab[0]);
            //$user->setRoles($row[1]);
            $user->setPassword($userPasswordHasher->hashPassword($user, $tab[1] ));
            $user->setNom($tab[2]);
            $user->setPrenom($tab[3]);
            $user->setTelephone($tab[4]);
            $user->setMail($tab[5]);
            $user->setSite($siteRepository->find($tab[6]));
            $user->setAdministrateur(false);
            $user->setActif(true);



            // Assuming the CSV structure is something like: username,email,password


            //$user->addSortiesOrganisee($row[10]);
            //$user->addSortiesParticipee($row[11]);
//            $user->setPhoto($row[9]);
//            $user->setImageSize($row[10]);
//            $user->setUpdatedAt($row[11]);


            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
    }
}