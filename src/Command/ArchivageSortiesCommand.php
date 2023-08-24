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
    name: 'ArchivageSorties',
    description: 'Les sorties réalisées depuis plus d\'un mois sont archivées',
)]
class ArchivageSortiesCommand extends Command
{

    private mixed $etatRepository;
    private mixed $sortieRepository;
    private mixed $entityManager;

    public function __construct(EtatRepository$etatRepository, SortieRepository$sortieRepository, EntityManagerInterface $entityManager)
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


        // Obtenez la date actuelle
        $now = new \DateTime();
        $sorties = $this->sortieRepository->findAllCommand();
        $archivee=$this->etatRepository->findOneBy(["id" => 7]);
        $passee = $this->etatRepository->findOneBy(["id" => 5]);
        foreach ($sorties as $sortie) {
            $interval=$now->diff($sortie->getDateHeureFin());
            $joursDifference = $interval->format('%a');
            if ($joursDifference>30 && $sortie->getEtat() == $passee) {
                $sortie->setEtat($archivee);
            }
            $this->entityManager->persist($sortie);
        }
        $this->entityManager->flush();
        $io->success('Archivage des sorties');

        return Command::SUCCESS;
    }
}
