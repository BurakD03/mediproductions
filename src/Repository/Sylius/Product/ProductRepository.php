<?php

namespace App\Repository\Sylius\Product;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository
{
    // public function findAllByOnHand(int $limit = 8): array
    // {
    //     return $this->createQueryBuilder('o')
    //         ->addSelect('variant')
    //         ->addSelect('translation')
    //         ->leftJoin('o.variants', 'variant')
    //         ->leftJoin('o.translations', 'translation')
    //         ->addOrderBy('variant.onHand', 'ASC')
    //         ->setMaxResults($limit)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    public function findAllProductVariant(string $codeCrm, ?int $limit = 2): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.code')
            ->innerJoin('o.productId', 'productId')
            ->andWhere('o.code LIKE :code')
            ->setParameter('code', '%' . $codeCrm . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByNameProductVariant(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
        ;
    }
}