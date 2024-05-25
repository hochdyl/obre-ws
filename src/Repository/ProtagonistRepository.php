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

    /**
     * @param string $gameSlug
     * @param string $protagonistSlug
     * @return array|null Returns a Protagonist object or null
     */
    public function findByGameAndSlug(string $gameSlug, string $protagonistSlug): Protagonist | null
    {
        return $this->createQueryBuilder('p')
            ->join('p.game', 'pg', 'WITH', 'pg.slug = :gameSlug')
            ->andWhere('p.slug = :protagonistSlug')
            ->setParameter('gameSlug', $gameSlug)
            ->setParameter('protagonistSlug', $protagonistSlug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Protagonist[] Returns an array of Protagonist objects
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

    //    public function findOneBySomeField($value): ?Protagonist
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
