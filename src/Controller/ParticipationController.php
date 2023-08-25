<?php

namespace App\Controller;

use App\Entity\Sortie;
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
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
      //  int $id
    ): Response
    {
        $user = $this->getUser();
      //  $sortie = $sortieRepository->findOneBy(["id" => $id]);
        $places = count($sortie->getParticipants());

        if(!$sortie->getParticipants()->contains($user) && $places < $sortie->getNbInscriptionsMax()){
            $sortie->addParticipant($user);
            $entityManager->persist($user);
            $entityManager->flush();

        }

        return $this->redirectToRoute('sortie_liste');
    }
    #[Route('desistement/{id}', name: 'app_desistement', requirements: ["id"=>"\d+"])]
    public function desistement(
        Sortie $sortie,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
        //  int $id
    ): Response
    {
        $user = $this->getUser();
        //  $sortie = $sortieRepository->findOneBy(["id" => $id]);

        if($sortie->getParticipants()->contains($user)){
            $sortie->removeParticipant($user);
            $entityManager->persist($user);
            $entityManager->flush();

        }

        return $this->redirectToRoute('sortie_liste');
    }
}
