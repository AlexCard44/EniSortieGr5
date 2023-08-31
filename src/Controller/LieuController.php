<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route('/liste', name: '_liste')]
    public function liste(
        LieuRepository $lieuRepository
    ): Response
    {
        // On vérifie si l'utilisateur est bien connecté
        // Dans le cas contraire, on l'envoie vers une page d'erreur pour qu'il puisse se connecter
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }
        // On fait appel à une fonction de requête SQL personnalisée afin de récupérer l'ensemble des enregistrements de la table Lieux (selon quelques critères) et de les afficher
        $lieux = $lieuRepository->findAllCustom();
        return $this->render('lieu/liste.html.twig',
            compact('lieux')
        );
    }

    #[Route('/creation', name: '_creation')]
    public function creation(
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {

        // On vérifie si l'utilisateur est bien connecté
        // Dans le cas contraire, on l'envoie vers une page d'erreur
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // On instancie un nouvel objet Lieu et on créé un nouveau formulaire de type lieu (lié à l'entité Lieu) et une nouvelle requête
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        // Si le formulaire est soumis, valide et que l'utilisateur a bien cliqué sur le bouton dénommé 'Enregistrer' alors on prépare la requête et on l'envoie en base de données
        // Ajout d'une notification de succès qui apparaitra sur la page de redirection
        if ($lieuForm->isSubmitted() && $lieuForm->isValid() && 'Enregistrer' === $lieuForm->getClickedButton()->getName()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success', $lieu->getNom() . ' a bien été créé ! ');
            return $this->redirectToRoute('lieu_liste');
        }
        return $this->render('lieu/creation.html.twig',
            [
                'lieuForm' => $lieuForm->createView(),
            ]);
    }

    #[Route('/edit/{id}',
        name: '_edit',
        requirements: ["id" => "\d+"])]
    public function edit(
        Lieu                   $lieu,
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {

        // On vérifie si l'utilisateur est bien connecté
        // Dans le cas contraire, on l'envoie vers une page d'erreur
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // On créé un nouveau formulaire de type lieu (lié à l'entité Lieu) et une nouvelle requête
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        // Si le formulaire est soumis, valide et que l'utilisateur a bien cliqué sur le bouton dénommé 'Supprimer' alors on prépare la requête et on l'envoie en base de données pour supprimer l'enregistrement correspondant
        // Si l'utilisateur clique sur un autre bouton (donc le bouton 'Enregistrer') on envoie une requête de modification en BDD.
        // Ajout d'une notification de succès en cas de suppression ou de modification de l'enregistrement
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            if ("Supprimer" === $lieuForm->getClickedButton()->getName()) {
                $entityManager->remove($lieu);
                $entityManager->flush();
                $this->addFlash('success', 'La suppression a bien été effectuée');
            } else {
                $this->addFlash('success', $lieu->getNom() . ' a bien été modifié !');
                $entityManager->flush();
            }
            return $this->redirectToRoute('lieu_liste');
        }
        return $this->render('lieu/edit.html.twig',
            [
                'lieu' => $lieu,
                'lieuForm' => $lieuForm
            ]);
    }
}
