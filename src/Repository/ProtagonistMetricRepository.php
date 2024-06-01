<?php

namespace App\Repository;

use App\Entity\ProtagonistMetric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProtagonistMetric>
 *
 * @method ProtagonistMetric|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProtagonistMetric|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProtagonistMetric[]    findAll()
 * @method ProtagonistMetric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProtagonistMetricRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProtagonistMetric::class);
    }

//    /**
//     * @return ProtagonistMetric[] Returns an array of ProtagonistMetric objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProtagonistMetric
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
