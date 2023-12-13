<?php

namespace App\Repository;

use App\Entity\MeetEval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MeetEval>
 *
 * @method MeetEval|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetEval|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetEval[]    findAll()
 * @method MeetEval[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetEvalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetEval::class);
    }

//    /**
//     * @return MeetEval[] Returns an array of MeetEval objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MeetEval
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
