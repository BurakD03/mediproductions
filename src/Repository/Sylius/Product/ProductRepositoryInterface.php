<?php

declare(strict_types=1);

namespace App\Repository\Sylius\Product;

use Sylius\Component\Core\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    // public function findAllProductVariant(string $codeCrm, ?int $limit = 2): array;
    public function findByNameProductVariant(string $name, string $locale): array;
}