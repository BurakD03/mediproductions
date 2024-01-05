<?php

namespace App\Repository;

use App\Entity\SubscriptionPaymentSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubscriptionPaymentSchedule>
 *
 * @method SubscriptionPaymentSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriptionPaymentSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriptionPaymentSchedule[]    findAll()
 * @method SubscriptionPaymentSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionPaymentScheduleRepository extends EntityRepository
{


//    /**
//     * @return SubscriptionPaymentSchedule[] Returns an array of SubscriptionPaymentSchedule objects
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

//    public function findOneBySomeField($value): ?SubscriptionPaymentSchedule
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
