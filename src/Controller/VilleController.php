<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'ville')]
class VilleController extends AbstractController
{

    #[Route('/liste', name: '_liste')]
    public function liste(
        VilleRepository $villeRepository
    ): Response
    {
        // Vérifier si l'utiliasteur est bien connecté
        try {
            $username = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }
        // Affichage de l'ensemble des enregistrements dans la table Ville selon une requête personnalisée
        $villes = $villeRepository->findAllCustom();
        return $this->render('ville/liste.html.twig',
            compact('villes')
        );
    }


    #[Route('/creation', name: '_creation')]
    public function creation(
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {
        $ville = new Ville();

        // Vérifier si l'utiliasteur est bien connecté
        try {
            $username = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // On créé le formulaire lié à l'entité Ville et la requête associée
        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        // Si le formulaire est soumis et valide, on envoie la requête poru création de l'enregistrement en BDD
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', $ville->getNom() . ' a bien été créée !');
            return $this->redirectToRoute('ville_liste');
        }

        // On envoie à la vue le formulaire
        return $this->render('ville/creation.html.twig',
            [
                'villeForm' => $villeForm->createView(),
            ]);
    }


    #[Route('/delete/{id}',
        name: '_delete',
        requirements: ["id"=>"\d+"])]
    public function delete(
        Ville $ville,
        EntityManagerInterface $entityManager
    ):Response
    {
        // Vérifier si l'utilisateur est bien connecté
        // Dans le cas contraire, on envoie l'utilisateur vers une page d'erreur qui lui demande de se connecter
        try {
            $username = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // Si l'utilisateur a cliqué sur le bouton "Supprimer" accessible depuis la liste des Villes,
        // Alors on envoie une requête pour supprimer l'enregistrement en BDD
        $entityManager->remove($ville);
        $entityManager->flush();
        $this->addFlash('success', 'La suppression a bien été effectuée !');
        return $this->redirectToRoute('ville_liste');

    }
}
