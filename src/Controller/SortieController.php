<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\SortiesFiltre;
use App\Form\SortieFiltreType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{

    #[Route('/liste', name: '_liste')]
    public function liste(
        SortieRepository      $sortieRepository,
        Request               $request,
        UtilisateurRepository $utilisateurRepository
    ): Response

    {
        // On vérifie si l'utilisateur est bien connecté afin de récupérer son identifiant
        // Dans le cas contraire, on l'envoie vers une page d'erreur lui demandant de se connecter
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // On recherche le profil de l'utilisateur (l'enregistrement correspondant en BDD) en fonction de l'identifiant récupéré plus haut
        $profil = $utilisateurRepository->findOneBy(['username' => $userActuel]);

        // On instancie un nouvel objet de l'entité SortiesFiltre (entité que nous avons créé pour nous aider à filter le listing des enregistrements de la table sortie en fonction de certains critères)
        $data = new SortiesFiltre();
        $data->sortiesOrganisees = $request->get('sortiesOrganisees', false);
        $form = $this->createForm(SortieFiltreType::class, $data);
        $form->handleRequest($request);
        // On affiche par défaut via une fonction de requête personnalisée, l'ensemble des enregistrements de la table Sortie qui sont à minima Publié tout en excluant les enregistrements archivés
        $sorties = $sortieRepository->findAllCustom();

        // Une fois le formulaire soumis (checkbox cochées ou non, barre de recherche remplie ou non)
        // On lance une fonction de requêtes personnalisées pour afficher les enregistrements respectant les critères demandés (cf. détails fonction)
        if ($form->isSubmitted() && $form->isValid()) {
            $sorties = $sortieRepository->findSearch($data, $profil);
            return $this->render('sortie/liste.html.twig', [
                'form' => $form->createView(),
                'sorties' => $sorties
            ]);
        }
        // On envoie le formulaire à la vue
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
        // On instancie un nouvel objet de l'entité Sortie
        $sortie = new Sortie();

        // Définir l'état comme "créée" à la création de l'occurrence "sortie"
        $etat = $etatRepository->findOneBy(["id" => 1]);
        $sortie->setEtat($etat);

        // Définir l'organisateur en fonction de l'utilisateur connecté
        // Dans le cas contraire on renvoie l'utilisateur vers une page d'erreur
        try {
            $sortie->setOrganisateur($this->getUser()); // ça me donne l'objet user
        } catch (\Throwable$throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // Rechercher l'identifiant de l'utilisateur connecté
        // Dans le cas contraire on renvoie l'utilisateur vers une page d'erreur
        try {
            $username = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        // On créé un nouveau formulaire de type Sortie (lié à l'entité Sortie) et une nouvelle requête
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        // Chercher l'enregistrement de la table Utilisateur rattaché à l'username, récupérer l'enregistrement de la table Site qui est rattaché, et l'affecter au nouvel objet en cours
        $user = $utilisateurRepository->findOneBy(["username" => $username]);
        $site = $user->getSite();
        $sortie->setSite($site);

        // Si le formulaire est soumis et valide, alors on prépare la requête et on l'envoie en base de données
        // Si l'utilisateur a cliqué sur "Publier" et non simplement sur "Enregistrer", l'attribut estPublié du nouvel objet passe en vrai donc il sera visible par les autres utilisateurs (ce qui n'est pas le cas des objets créé mais non publiés)
        // Ajout d'une notification de succès qui apparaitra sur la page de redirection
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ("Publier" === $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepository->findOneBy(["id" => 2]);
                $sortie->setEtat($etat);
                $sortie->setEstPublie(true);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', $sortie->getNom() . ' a bien été créée !');
            return $this->redirectToRoute('sortie_liste');
        }

        // On envoie à la vue le formulaire
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

        // Si l'utilisateur connecté n'est pas l'organisateur de l'objet Sortie en paramètre de la fonction, ou Si l'objet n'est pas en état "créé" ou "publier"
        // On ne peut modifier l'objet Sortie, on renvoie l'utilisateur vers une page d'erreur
        if ($this->getUser() !== $sortie->getOrganisateur() || ($sortie->getEtat()->getId() != 1 && $sortie->getEtat()->getId() != 2)) {
            return $this->render('@Twig/Exception/error403.html.twig');
        }
        // Création du formulaire et de la requête associée
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        // Si le formulaire est soumis et valide,
        // Si l'utilisateur clique sur publier, la Sortie passe en ouvert (état) et l'attribut estPublié passe en vrai
        // Si l'utilisateur clique sur annuler, la Sortie passe en annulé (état)
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if("Publier" === $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepository->findOneBy(["id" => 2]);
                $sortie->setEtat($etat);
                $sortie->setEstPublie(true);
            } elseif ("Annuler" === $sortieForm->getClickedButton()->getName()) {
                $etat = $etatRepository->findOneBy(["id" => 6]);
                $sortie->setEtat($etat);
                $sortie->setEstPublie(false);
            }
            // Envoi de la requête pour modification en base de données
            $entityManager->flush();
            $this->addFlash('success', 'Les modifications ont bien été apportées à la sortie : ' . $sortie->getNom());
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
        // On vérifie si l'utilisateur est bien connecté pour avoir l'accès aux détails de l'objet
        // Dans le cas contraire on l'envoie sur une page d'erreur lui demandant de se connecter
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }
        return $this->render('sortie/details.html.twig',
            [
                'sortie' => $sortie
            ]);
    }
}
