<?php

namespace App\Repository;

use App\Entity\AppVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppVersion>
 *
 * @method AppVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppVersion[]    findAll()
 * @method AppVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppVersion::class);
    }

    //    /**
    //     * @return AppVersion[] Returns an array of AppVersion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AppVersion
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
