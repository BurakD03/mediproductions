<?php

namespace App\Repository;

use App\Entity\Licence;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Licence>
 *
 * @method Licence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Licence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Licence[]    findAll()
 * @method Licence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LicenceRepository extends EntityRepository
{


    public function findByNamePart(string $codeCrm, ?int $limit = 2): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.codeCrm')
            ->andWhere('o.codeCrm LIKE :code')
            ->setParameter('code', '%' . $codeCrm . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    // public function findAllProductVariant(string $codeCrm, ?int $limit = 2): array
    // {
    //     return $this->createQueryBuilder('o')
    //         ->select('o.codeCrm')
    //         ->from('App\Entity\NomDeVotreEntite', 'alias') // Remplacez "NomDeVotreEntite" par le nom réel de votre entité
    //         ->andWhere('alias.codeCrm LIKE :code')
    //         ->setParameter('code', '%' . $codeCrm . '%')
    //         ->setMaxResults($limit)
    //         ->getQuery()
    //         ->getResult();
    // }

//    /**
//     * @return Licence[] Returns an array of Licence objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Licence
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
