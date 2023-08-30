<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\SortiesFiltre;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findByUsername(Utilisateur $username)
    {
        return $this->createQueryBuilder('s')
            ->where(':utilisateur_connecte MEMBER OF s.participants')
            ->setParameter('utilisateur_connecte', $username)
            ->getQuery()
            ->getResult();

    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findAllCustom(): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('sortie')

            ->addOrderBy('sortie.nom','ASC')
            ->andWhere('sortie.estPublie=true')
            ->andWhere('sortie.etat!=7')
            ->setMaxResults(10)
            ->getQuery();
        $paginator = new Paginator($queryBuilder);
        return $paginator;
    }

    public function findAllCommand(): array
    {
        $queryBuilder = $this->createQueryBuilder('sortie'); // SELECT * FROM sortie
        return $queryBuilder->getQuery()->getResult();
    }

    public function findSearch(SortiesFiltre $sortiesFiltre, Utilisateur $utilisateur): Paginator
    {
       # $utilisateurId = $this->getUser()->getId();
        $query = $this

            ->createQueryBuilder('sortie')
            ->select('sortie');


        if(empty($sortiesFiltre->sortiesOrganisees) && empty($sortiesFiltre->sortiesPassees) && empty($sortiesFiltre->sortiesNonInscrit) && empty($sortiesFiltre->sortiesInscrit) && empty($sortiesFiltre->getName())) {
            $query = $query
                ->addOrderBy('sortie.nom','ASC')
                ->andWhere('sortie.estPublie=true')
                ->andWhere('sortie.etat!=7')
                ->setMaxResults(10);
        }

        if (!empty($sortiesFiltre->sortiesOrganisees)) {
            $query = $query
                ->andWhere('sortie.organisateur = :utilisateur')
                ->andWhere('sortie.etat != 7')
                ->setParameter('utilisateur', $utilisateur);
        }

        if (!empty($sortiesFiltre->sortiesPassees)) {
            $query = $query
                ->andWhere('sortie.etat = :etat')
                ->andWhere('sortie.estPublie = true')
                ->setParameter('etat', 5);
        }


            if (!empty($sortiesFiltre->sortiesNonInscrit)){
                $query=$query
                    ->andWhere('sortie.etat != 7')
                    ->andWhere('sortie.estPublie = true AND sortie.etat != 6 AND sortie.etat != 1 
                    OR 
                    (:utilisateur NOT MEMBER OF sortie.participants AND :utilisateur = sortie.organisateur AND (sortie.etat = 6 OR sortie.etat = 1))')
                    ->andWhere(':utilisateur NOT MEMBER OF sortie.participants')
                    ->setParameter('utilisateur', $utilisateur);
            }




            if (!empty($sortiesFiltre->sortiesInscrit)) {
                $query = $query
                    ->andWhere('sortie.etat != 7')
                    ->andWhere('sortie.estPublie = true AND sortie.etat != 6 AND sortie.etat != 1 AND :utilisateur MEMBER OF sortie.participants
                    OR
                    (:utilisateur MEMBER OF sortie.participants AND :utilisateur = sortie.organisateur AND (sortie.etat = 6 OR sortie.etat = 1))')
                  ->andWhere(':utilisateur MEMBER OF sortie.participants')
                  ->setParameter('utilisateur', $utilisateur);
            }

            if (!empty($sortiesFiltre->getName())){
                $query =$query
                    ->andWhere('sortie.etat != 7')
                    ->andWhere('sortie.estPublie = true AND sortie.etat != 1
                    OR 
                    (:utilisateur = sortie.organisateur)')
                    ->andWhere('sortie.nom LIKE :name')
                    ->setParameter('name', "%{$sortiesFiltre->getName()}%")
                    ->setParameter('utilisateur', $utilisateur)
                ;
            }

            if (!empty($sortiesFiltre->getDateTime())){
                $query=$query
                    ->andWhere('sortie.etat != 7')
                    ->andWhere('sortie.dateHeureDebut >= :dateTime ')
                    ->addOrderBy('sortie.dateHeureDebut', 'ASC')
                    ->andWhere('sortie.etat != 6 AND sortie.etat != 1 OR :utilisateur = sortie.organisateur')
                    ->setParameter('utilisateur', $utilisateur)
                    ->setParameter('dateTime', $sortiesFiltre->getDateTime());



            }




        $paginator = new Paginator($query);
        return $paginator;


    }
}
