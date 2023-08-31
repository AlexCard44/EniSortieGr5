<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{

    // Fonction pour rejoindre une sortie en tant que participant
    #[Route('/participation/{id}', name: 'app_participation', requirements: ["id" => "\d+"])]
    public function index(
        Sortie                 $sortie,
        EtatRepository         $etatRepository,
        EntityManagerInterface $entityManager,
    ): Response
    {
        // On vérifie si l'utilisateur est bien connecté
        // Dans le cas contraire, on l'envoie vers une page d'erreur lui demandant de se connecter
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // On vérifie que l'objet enregistré dans l'attribut "état" de la sortie en question, correspond bien à l'état "ouvert" (état qui permet de rejoindre une sortie)
        // On vérifie également que l'utilisateur est bien connecté pour pouvoir rejoindre une sortie
        // Dans le cas contraire, on renvoie l'utilisateur vers une page d'erreur
        $etatOuvert = $etatRepository->findOneBy(["id" => 2]);
        if ($sortie->getEtat() !== $etatOuvert || !$this->getUser()) {
            return $this->render('@Twig/Exception/error401.html.twig');
        } else {
            // On récupère l'objet de l'utilisateur connecté
            $user = $this->getUser();
            // On récupère le nombre de participants qui sont déjà inscrits à la sortie en question
            $places = count($sortie->getParticipants());

            // Si l'utilisateur n'est pas déjà inscrit à la sortie en question
            // Et qu'il reste encore des places de disponible (on compare le nombre de participants déjà inscrits au nombre de places max)
            // Alors on l'ajoute parmi les participants de la sortie et on met à jour la BDD, sans oublier la notification confirmant l'inscription
            if (!$sortie->getParticipants()->contains($user) && $places < $sortie->getNbInscriptionsMax()) {
                $sortie->addParticipant($user);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Vous êtes bien inscrit à la sortie ' . $sortie->getNom());
            }
            return $this->redirectToRoute('sortie_liste');
        }

    }

    // Fonction pour se désister d'une sortie à laquelle nous sommes inscrits
    #[Route('desistement/{id}', name: 'app_desistement', requirements: ["id" => "\d+"])]
    public function desistement(
        Sortie                 $sortie,
        EtatRepository         $etatRepository,
        EntityManagerInterface $entityManager,
    ): Response
    {
        // On vérifie si l'utilisateur est bien connecté
        // Dans le cas contraire, on l'envoie vers une page d'erreur
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // On vérifie que l'objet enregistré dans l'attribut "état" de la sortie en question, correspond bien à l'état "ouvert" (état qui permet de rejoindre une sortie et de s'en désister)
        // On vérifie également que l'utilisateur est bien connecté pour pouvoir récupérer l'objet utilisateur et le retirer des participants à la sortie
        // Dans le cas contraire, on renvoie l'utilisateur vers une page d'erreur
        $etatOuvert = $etatRepository->findOneBy(["id" => 2]);
        if ($sortie->getEtat() !== $etatOuvert || !$this->getUser()) {
            return $this->render('@Twig/Exception/error401.html.twig');
        } else {
            // On récupère l'objet de l'utilisateur connecté
            $user = $this->getUser();
            // On vérifie que l'utilisateur fasse bien partie de la liste des participants
            if ($sortie->getParticipants()->contains($user)) {
                $sortie->removeParticipant($user);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre désistement a bien été pris en compte pour la sortie ' . $sortie->getNom());
            }
            return $this->redirectToRoute('sortie_liste');
        }
    }
}
