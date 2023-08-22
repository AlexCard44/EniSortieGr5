<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        Request $request
    ): Response
    {
        $sortie = new Sortie();

        // Définir l'état comme "créée" à la création de l'occurrence "sortie"
        $etat=$etatRepository->findOneBy(["id"=>1]);
        $sortie->setEtat($etat);

        // TODO : définir l'organisateur ainsi que le site pour l''occurrence "sortie" avec une injection de dépendance comme pour l'état

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted()&&$sortieForm->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/creation.html.twig',
            [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

}
