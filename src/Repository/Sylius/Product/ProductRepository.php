<?php

namespace App\Repository\Sylius\Product;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository
{

    // public function findAllProductVariant(string $codeCrm, ?int $limit = 2): array
    // {
    //     return $this->createQueryBuilder('o')
    //         ->select('o.code')
    //         ->innerJoin('o.productId', 'productId')
    //         ->andWhere('o.code LIKE :code')
    //         ->setParameter('code', '%' . $codeCrm . '%')
    //         ->setMaxResults($limit)
    //         ->getQuery()
    //         ->getResult();
    // }

    public function findByNameProductVariant(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->select('translationProduct.name as TP, translationOption.value as TV, variants.id, variants.durationValue, variants.durationUnit')
            ->innerJoin('o.variants', 'variants')
            ->innerJoin('variants.translations', 'translation')
            ->innerJoin('o.translations', 'translationProduct')
            ->innerJoin('variants.optionValues', 'optionValue')
            ->innerJoin('optionValue.translations', 'translationOption')
            ->andWhere('variants.durationValue is not null')
            ->andWhere('translationProduct.name LIKE :name')
            ->andWhere('translation.locale = :locale')
            ->andWhere('translationProduct.locale = :locale')
            ->andWhere('translationOption.locale = :locale')
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByIdProductVariant(int $id, string $locale): ?string
    {
        $result = $this->createQueryBuilder('o')
            ->select("CONCAT(translationProduct.name, ' - ', variants.durationValue, ' ', variants.durationUnit)")
            ->innerJoin('o.variants', 'variants')
            ->innerJoin('variants.translations', 'translation')
            ->innerJoin('o.translations', 'translationProduct')
            ->innerJoin('variants.optionValues', 'optionValue')
            ->innerJoin('optionValue.translations', 'translationOption')
            ->andWhere('variants.durationValue is not null')
            ->andWhere('variants.id = :id')
            ->andWhere('translation.locale = :locale')
            ->andWhere('translationProduct.locale = :locale')
            ->andWhere('translationOption.locale = :locale')
            ->setParameter('id', $id)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);
        
        
        return $result !== null ? $result : '';    
    }
}