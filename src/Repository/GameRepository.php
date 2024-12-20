<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * Return games available for user
     *
     * One of the following statement is required :
     *
     * - Game is not closed
     * - Game is owned by user
     * - At least one of the game's protagonists is owned by user
     */
    public function findAllAvailableByUser(string $username): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.gameMaster', 'ggm')
            ->leftJoin('g.protagonists', 'gp')
            ->leftJoin('gp.owner', 'gpo')
            ->where('g.closed = 0')
            ->orWhere('ggm.username = :username')
            ->orWhere('gpo.username = :username')
            ->setParameter('username', $username)
            ->orderBy('g.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Game[] Returns an array of Game objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Game
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}