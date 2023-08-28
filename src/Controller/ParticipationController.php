<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{
    #[Route('/participation/{id}', name: 'app_participation', requirements: ["id"=>"\d+"])]
    public function index(
        Sortie $sortie,
        EtatRepository $etatRepository,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
      //  int $id
    ): Response
    {
        $etatOuvert=$etatRepository->findOneBy(["id"=>2]);
        if($sortie->getEtat()!==$etatOuvert || !$this->getUser()) {
            return $this->render('error401.html.twig');
        } else {
            $user = $this->getUser();
            //  $sortie = $sortieRepository->findOneBy(["id" => $id]);
            $places = count($sortie->getParticipants());

            if(!$sortie->getParticipants()->contains($user) && $places < $sortie->getNbInscriptionsMax()){
                $sortie->addParticipant($user);
                $entityManager->persist($user);
                $entityManager->flush();

            }
            $this->addFlash('success', 'Vous êtes bien inscrit à la sortie ' . $sortie->getNom());
            return $this->redirectToRoute('sortie_liste');
        }

    }
    #[Route('desistement/{id}', name: 'app_desistement', requirements: ["id"=>"\d+"])]
    public function desistement(
        Sortie $sortie,
        EtatRepository $etatRepository,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
        //  int $id
    ): Response
    {
        $etatOuvert=$etatRepository->findOneBy(["id"=>2]);
        if($sortie->getEtat()!==$etatOuvert || !$this->getUser()) {
            return $this->render('error401.html.twig');
        } else {
            $user = $this->getUser();
            //  $sortie = $sortieRepository->findOneBy(["id" => $id]);

            if ($sortie->getParticipants()->contains($user)) {
                $sortie->removeParticipant($user);
                $entityManager->persist($user);
                $entityManager->flush();

            }
            $this->addFlash('success', 'Votre désistement a bien été pris en compte pour la sortie ' . $sortie->getNom());
            return $this->redirectToRoute('sortie_liste');
        }
    }
}
