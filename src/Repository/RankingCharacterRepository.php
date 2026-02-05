<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\RankingCharacter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RankingCharacter>
 */
class RankingCharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RankingCharacter::class);
    }

    public function ordenarRankingEstadisticas(Category $category): array
    {
        return $this->createQueryBuilder('rc')
            ->select([
                'c.id AS character_id',
                'c.name AS character_name',
                'c.portrait_path AS portrait',
                'AVG(rc.position) AS avgPosition',
                'COUNT(rc.id) AS totalRankings'
            ])
            ->innerJoin('rc.character', 'c')
            ->innerJoin('rc.ranking', 'r')
            ->where('r.category = :category')
            ->setParameter('category', $category)
            ->groupBy('c.id, c.name, c.portrait_path')
            ->orderBy('avgPosition', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

//    /**
//     * @return RankingCharacter[] Returns an array of RankingCharacter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RankingCharacter
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
