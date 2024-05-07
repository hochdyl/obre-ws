<?php

namespace App\Repository;

use App\Entity\Protagonist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Protagonist>
 *
 * @method Protagonist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Protagonist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Protagonist[]    findAll()
 * @method Protagonist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProtagonistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Protagonist::class);
    }

    //    /**
    //     * @return Protagonist[] Returns an array of Protagonist objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Protagonist
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
