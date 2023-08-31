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

        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', $ville->getNom() . ' a bien été créée !');
            return $this->redirectToRoute('ville_liste');
        }

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
        // Vérifier si l'utiliasteur est bien connecté
        try {
            $username = $this->getUser()->getUserIdentifier();
        } catch (\Throwable $throwable) {
            return $this->render('@Twig/Exception/error401.html.twig');
        }
        $entityManager->remove($ville);
        $entityManager->flush();
        $this->addFlash('success', 'La suppression a bien été effectuée !');
        return $this->redirectToRoute('ville_liste');

    }


}
