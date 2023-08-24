<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\MonProfilType;
use App\Form\MotDePasseType;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        return $this->render('profil/profil.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    #[Route('/monProfil', name: 'profil_monProfil')]
    public function monProfil(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response
    {
        $user = $this->getUser();
        $sorties = [];

//        $profil = new Utilisateur();
//        echo ($profil->getNom());
//        $profil->setUsername($user->getUserIdentifier());
//        $profil->setNom($user->getNom());
//        $profil->setPrenom($user->getPrenom());
//        $profil->setMail($user->getMail());
//        $profil->setTelephone($user->getTelephone());
//        $profil->setSite($user->getSite());
//        $profil->setUserIdentifier()

        $profil= $utilisateurRepository->findOneBy(['username'=>$user->getUserIdentifier()]);
        $sorties = $sortieRepository->findByUsername($profil);
        //dd($sorties);
        $form = $this->createForm(MonProfilType::class, $profil);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
//            $profil->setPassword(
//                $hasher->hashPassword(
//                    $profil,
//                    $form->get('plainPassword')->getData()
//                )
//            );
            $entityManager->persist($profil);
            $entityManager->flush();
            $this->addFlash('success', 'Profil modifié avec succès');

            return $this->redirectToRoute('sortie_liste');

        }

        return $this->render('profil/monProfil.html.twig', [
            'form' => $form->createView(),
            'sorties' => $sorties
        ]);
    }

    #[Route('/modifMotDePasse', name: 'profil_modifMdp')]
    public function modifMdp(Request $request,
                             UserPasswordHasherInterface $passwordHasher,
                             UtilisateurRepository $utilisateurRepository,
                             EntityManagerInterface $entityManager

    ): Response
    {
        $userActuel = $this->getUser();


        $profil= $utilisateurRepository->findOneBy(['username'=>$userActuel->getUserIdentifier()]);

//        $form = $this->createForm(MotDePasseType::class,$profil);
        $form = $this->createForm(MotDePasseType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

          $oldPassword = $form->get('password')->getData();
//          dd($passwordHasher->isPasswordValid($this->getUser(), $oldPassword));
//            dump($profil->getPassword());
//            dd($passwordHasher->isPasswordValid($profil, '123456'));
            //verif de la correspondance
            if($passwordHasher->isPasswordValid($profil,$oldPassword )){
                $newMdpHash = $passwordHasher->hashPassword($profil, $form->get('newPassword')->getData());
                $profil->setPassword($newMdpHash);
                $entityManager ->persist($profil);
                $entityManager ->flush();
                return $this->redirectToRoute('sortie_liste');
            }



            }




        return $this->render('profil/motDePasse.html.twig', [
            'controller_name' => 'ProfilController',
            'form'=>$form->createView()
        ]);


    }
}
