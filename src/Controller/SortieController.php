<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\SortiesFiltre;
use App\Form\SortieFiltreType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\SortiesFiltreRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/liste', name: '_liste')]
    public function liste(
        SortieRepository        $sortieRepository,
        Request                 $request,
        UtilisateurRepository $utilisateurRepository
    ): Response

    {

        $userActuel = $this->getUser();
        $profil = $utilisateurRepository->findOneBy(['username' => $userActuel->getUserIdentifier()]);
        $data = new SortiesFiltre();
        $data->sortiesOrganisees = $request->get('sortiesOrganisees', false);
        $form = $this->createForm(SortieFiltreType::class, $data);
        $form->handleRequest($request);

        $sorties = $sortieRepository->findAllCustom();

        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sortieRepository->findSearch($data, $profil);
            return $this->render('sortie/liste.html.twig', [
                'form' => $form->createView(),
                'sorties' => $sorties
            ]);
        }



        return $this->render('sortie/liste.html.twig', [
            'form' => $form->createview(),
            'sorties' => $sorties
        ]);
    }

    #[Route('/creation', name: '_creation')]
    public function creation(
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository,
        UtilisateurRepository  $utilisateurRepository,
        Request                $request
    ): Response
    {
        $sortie = new Sortie();

        // Définir l'état comme "créée" à la création de l'occurrence "sortie"
        $etat = $etatRepository->findOneBy(["id" => 1]);
        $sortie->setEtat($etat);

        // Définir l'organisateur en fonction de l'utilisateur connecté
        try {
            $sortie->setOrganisateur($this->getUser()); // ça me donne l'objet user
        } catch (\Throwable$throwable) {
            return $this->render('error401.html.twig');
        }

        // Définir le site rattaché à l'organisateur
        try {
            $username = $this->getUser()->getUserIdentifier(); // ça me donne l'username de l'utilisateur actuellement connecté
        } catch (\Throwable $throwable) {
            return $this->render('error401.html.twig');
        }

        // Chercher le site rattaché au username
        $user = $utilisateurRepository->findOneBy(["username" => $username]);
        $site = $user->getSite();
        $sortie->setSite($site);

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);


        $sortie->setSite($this->getUser()->getSite());
     
        if ($sortieForm->isSubmitted()&&$sortieForm->isValid()){
          
            if ("Publier" === $sortieForm->getClickedButton()->getName()){
                $etat = $etatRepository->findOneBy(["id" => 2]);
                $sortie->setEtat($etat);
                $sortie->setEstPublie(true);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/creation.html.twig',
            [
                'sortieForm' => $sortieForm->createView(),
            ]);
    }

    #[Route('/edit/{id}',
        name: '_edit',
        requirements: ["id" => "\d+"])]
    public function edit(
        Sortie                 $sortie,
        EtatRepository         $etatRepository,
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {

        if($this->getUser() !== $sortie->getOrganisateur() || ($sortie->getEtat()->getId() != 1 && $sortie->getEtat()->getId() != 2)) {
            return $this->render('error401.html.twig');
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);


        // TODO: optimiser le code des if
        if ($sortieForm->isSubmitted() && $sortieForm->isValid() && "Enregistrer" === $sortieForm->getClickedButton()->getName()) {
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        if ($sortieForm->isSubmitted() && $sortieForm->isValid() && "Publier" === $sortieForm->getClickedButton()->getName()) {
            $etat = $etatRepository->findOneBy(["id" => 2]);
            $sortie->setEtat($etat);
            $sortie->setEstPublie(true);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        if ($sortieForm->isSubmitted() && $sortieForm->isValid() && "Annuler" === $sortieForm->getClickedButton()->getName()) {
            $etat = $etatRepository->findOneBy(["id" => 6]);
            $sortie->setEtat($etat);
            $sortie->setEstPublie(false);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/edit.html.twig',
            [
                'sortie' => $sortie,
                'sortieForm' => $sortieForm
            ]);
    }

    #[Route('/details/{id}', name: '_details')]
    public function afficherDetails(
        Sortie $sortie
    ): Response
    {
        return $this->render('sortie/details.html.twig',
            [
                'sortie' => $sortie
            ]);
    }

    #[Route]
    public function filtre(SortiesFiltreRepository $sortiesFiltreRepository, Request $request)
    {
        $data = new SortiesFiltre();
        $data->sortiesOrganisees = $request->get('sortiesOrganisees', false);
        $form = $this->createForm(SortiesFiltre::class, $data);
        $form->handleRequest($request);
        $products = $sortiesFiltreRepository->findSearch($data);
        return $this->render('sortie/list.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }


}
