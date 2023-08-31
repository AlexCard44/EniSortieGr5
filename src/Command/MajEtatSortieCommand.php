<?php

namespace App\Command;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'MajEtatSortie',
    description: 'Mise à jour des états des sorties en fonction de la date',
)]
class MajEtatSortieCommand extends Command
{

    private EtatRepository $etatRepository;
    private SortieRepository $sortieRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EtatRepository $etatRepository, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->etatRepository = $etatRepository;
        $this->sortieRepository = $sortieRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Rechercher toutes les sorties enregistrées et les stocker dans une variable
        $sorties = $this->sortieRepository->findAllCommand();

        // On va chercher tous les enregistrements de l'entité "etat" qui nous seront utiles pour la MAJ des attributs "etat" des enregistrement de l'entité "sortie"
        $annulee = $this->etatRepository->findOneBy(["id" => 6]);
        $archivee = $this->etatRepository->findOneBy(["id" => 7]);
        $cloture = $this->etatRepository->findOneBy(["id" => 3]);
        $enCours = $this->etatRepository->findOneBy(["id" => 4]);
        $passee = $this->etatRepository->findOneBy(["id" => 5]);
        $creee = $this->etatRepository->findOneBy(["id" => 1]);
        $now = new \DateTime();

        // Pour chaque enregistrement de Sortie, on applique une logique métier de calcul entre le moment où on lance la commande et l'un des attributs de date de l'enregistrement de sortie
        // et on lui affecte un nouvel objet "etat" pour l'attribut "etat" de chaque enregistrement de Sortie.
        foreach ($sorties as $sortie) {
            // On vérifie si l'état de l'enregistrement n'est pas annulé, ni archivé, ni créé (ces enregistrements ne seront pas affectés par la commande pour certaines raisons du cahier des charges)
            if ($sortie->getEtat() !== $annulee && $sortie->getEtat() !== $archivee && $sortie->getEtat() !== $creee) {
                // Si la date limite des inscriptions est dépassée, la sortie passe en état clôturé
                if ($now > $sortie->getDateLimiteInscription()) {
                    $sortie->setEtat($cloture);
                }
                // Si la date de début de la sortie est dépassée, la sortie passe en état "en cours"
                if ($now > $sortie->getDateHeureDebut()) {
                    $sortie->setEtat($enCours);
                }

                // Si la date de fin de la sortie est dépassée, la sortie passe en état "passée"
                if ($now > $sortie->getDateHeureFin()) {
                    $sortie->setEtat($passee);
                }
            }
            // Préparation de la requête
            $this->entityManager->persist($sortie);
        }
        // On met à jour la Base de données
        $this->entityManager->flush();
        $io->success('Mise à jour des états des sorties effectuées');
        return Command::SUCCESS;
    }
}