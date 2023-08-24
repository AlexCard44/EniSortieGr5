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
        $sorties = $this->sortieRepository->findAllCommand();
        $annulee=$this->etatRepository->findOneBy(["id" => 6]);
        $archivee=$this->etatRepository->findOneBy(["id" => 7]);
        $cloture = $this->etatRepository->findOneBy(["id" => 3]);
        $enCours = $this->etatRepository->findOneBy(["id" => 4]);
        $passee = $this->etatRepository->findOneBy(["id" => 5]);
        $creee = $this->etatRepository->findOneBy(["id" => 1]);
        $now = new \DateTime();
        foreach ($sorties as $sortie) {
            if ($sortie->getEtat() !== $annulee && $sortie->getEtat() !== $archivee && $sortie->getEtat() !== $creee) {
                if ($now > $sortie->getDateLimiteInscription()) {
                    $sortie->setEtat($cloture);
                }
                if ($now > $sortie->getDateHeureDebut()) {
                    $sortie->setEtat($enCours);
                }
                if ($now > $sortie->getDateHeureFin()) {
                    $sortie->setEtat($passee);
                }
            }

            $this->entityManager->persist($sortie);
        }
        $this->entityManager->flush();
        $io->success('Mise à jour des états des sorties effectuées');
        return Command::SUCCESS;
    }
}