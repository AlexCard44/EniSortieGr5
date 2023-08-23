<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;

#[Route('/sortie', name:'sortie')]
class SortieController extends AbstractController
{
    #[Route('/liste', name: '_liste')]
    public function liste(
        SortieRepository $sortieRepository
    ): Response
    {
        $sorties=$sortieRepository->findAllCustom();
        return $this->render('sortie/liste.html.twig',
        compact('sorties'));
    }

    #[Route('/creation', name: '_creation')]
    public function creation(
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository,
        UtilisateurRepository $utilisateurRepository,
        Request $request
    ): Response
    {
        $sortie = new Sortie();

        // Définir l'état comme "créée" à la création de l'occurrence "sortie"
        $etat=$etatRepository->findOneBy(["id"=>1]);
        $sortie->setEtat($etat);

        // Définir l'organisateur en fonction de l'utilisateur connecté
        try {
            $sortie->setOrganisateur($this->getUser()); // ça me donne l'objet user
        } catch (\Throwable$throwable) {
            return $this->render('erreur.html.twig');
        }

        // Définir le site rattaché à l'organisateur
        try {
            $username=$this->getUser()->getUserIdentifier(); // ça me donne l'username de l'utilisateur actuellement connecté
        } catch (\Throwable $throwable) {
            return $this->render('erreur.html.twig');
        }

        // Chercher le site rattaché au username
        $user=$utilisateurRepository->findOneBy(["username"=>$username]);
        $site=$user->getSite();
        $sortie->setSite($site);

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        $sortie->setSite($this->getUser()->getSite());

        if($sortieForm->isSubmitted()&&$sortieForm->isValid() && "Enregistrer" === $sortieForm->getClickedButton()->getName()) {
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        if($sortieForm->isSubmitted()&&$sortieForm->isValid() && "Publier" === $sortieForm->getClickedButton()->getName()) {
            $etat=$etatRepository->findOneBy(["id"=>2]);
            $sortie->setEtat($etat);
            $sortie->setEstPublie(true);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        $sortieForm->remove("Annuler");

        return $this->render('sortie/creation.html.twig',
            [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    #[Route('/edit/{id}',
        name: '_edit',
        requirements: ["id"=>"\d+"])]
    public function edit(
        Sortie $sortie,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        if($this->getUser() !== $sortie->getOrganisateur()) {
            return $this->render('erreur.html.twig');
        }
        $sortieForm= $this->createForm(SortieType::class,$sortie);
        $sortieForm->handleRequest($request);


         // TODO: optimiser le code des if
        if($sortieForm->isSubmitted()&&$sortieForm->isValid()  && "Enregistrer" === $sortieForm->getClickedButton()->getName()) {
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        if($sortieForm->isSubmitted()&&$sortieForm->isValid() && "Publier" === $sortieForm->getClickedButton()->getName()) {
            $etat=$etatRepository->findOneBy(["id"=>2]);
            $sortie->setEtat($etat);
            $sortie->setEstPublie(true);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        if($sortieForm->isSubmitted()&&$sortieForm->isValid() && "Annuler" === $sortieForm->getClickedButton()->getName()) {
            $etat=$etatRepository->findOneBy(["id"=>6]);
            $sortie->setEtat($etat);
            $sortie->setEstPublie(false);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        if ($sortie->isEstPublie()){
            $sortieForm->remove("Publier");
        }

        return $this->render('sortie/edit.html.twig',
        [
            'sortie'=>$sortie,
            'sortieForm'=>$sortieForm
        ]);
    }

    #[Route('/details/{id}',name:'_details')]
    public function afficherDetails(
        Sortie $sortie
    ):Response
    {
       return $this->render('sortie/details.html.twig',
       [
           'sortie'=>$sortie
       ]);
    }

}
