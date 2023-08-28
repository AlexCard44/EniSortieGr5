<?php

namespace App\Repository;

use App\Entity\SortiesFiltre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SortiesFiltre>
 *
 * @method SortiesFiltre|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortiesFiltre|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortiesFiltre[]    findAll()
 * @method SortiesFiltre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesFiltreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortiesFiltre::class);
    }


    public function findSearch(SortiesFiltre $sortiesFiltre) :Paginator
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.sortiesOrganisees', 'c');

        if (!empty($sortiesFiltre->sortiesInscrit)) {
            $query = $query
                ->andWhere('p.name LIKE :sortiesOrganisees')
                ->setParameter('sortiesOrganisees', "%{$sortiesFiltre->sortiesOrganisees}%");


        }
        return $this->paginator->paginate(
            $query,
            $sortiesFiltre->sortiesOrganisees,

        );
    }

//        private function getSearchQuery(SortiesFiltre $sortiesFiltre): QueryBuilder
//        {
//        }

//    /**
//     * @return SortiesFiltre[] Returns an array of SortiesFiltre objects
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

//    public function findOneBySomeField($value): ?SortiesFiltre
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
