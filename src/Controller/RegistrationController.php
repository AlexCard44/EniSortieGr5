<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\CsvType;
use App\Form\RegistrationFormType;
use App\Repository\SiteRepository;
use App\Service\UserCSVImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             UserCSVImporter $csvImporter,
                             SiteRepository $siteRepository): Response
    {

        //Formulaire pour ajouter un seul utilisateur
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //$user->setRoles(['ROLE_VALID'])
            ;

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        //Formulaire pour ajouter plusieurs utilisateurs à la fois
        $formCsv = $this->createForm(CsvType::class);
        $formCsv->handleRequest($request);

        if ($formCsv->isSubmitted() && $formCsv->isValid()) {
            $csvFile = $formCsv->get('csvFile')->getData();
            $csvImporter->importCSV($csvFile, $siteRepository, $userPasswordHasher);

            $this->addFlash('success', 'Utilisateurs importés avec succès');

            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'csvForm' => $formCsv->createView()
        ]);
    }
}
