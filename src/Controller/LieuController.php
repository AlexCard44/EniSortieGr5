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
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }
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
        $lieu = new Lieu();
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

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
        try {
            $userActuel = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

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
