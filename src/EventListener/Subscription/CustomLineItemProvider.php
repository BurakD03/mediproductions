<?php

declare(strict_types=1);

namespace App\EventListener\Subscription;

use App\Entity\Product\ProductVariant;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemImagesProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProvider as LineItemProvider;

final class CustomLineItemProvider implements LineItemProviderInterface
{
    /** @var LineItemImagesProviderInterface */
    private $lineItemImagesProvider;

    /** @var LinetItemNameProviderInterface */
    private $lineItemNameProvider;

    private $decoratedLineItemProvider;

    public function __construct(
        LineItemImagesProviderInterface $lineItemImagesProvider,
        LinetItemNameProviderInterface $lineItemNameProvider,
        // LineItemProvider $decoratedLineItemProvider
    ) {
        $this->lineItemImagesProvider = $lineItemImagesProvider;
        $this->lineItemNameProvider = $lineItemNameProvider;
        // $this->decoratedLineItemProvider = $decoratedLineItemProvider;
    }

    public function getLineItem(OrderItemInterface $orderItem): ?array
    {
        /** @var OrderInterface|null $order */
        $order = $orderItem->getOrder();
 
        if (null === $order) {
            return null;
        }
        
        $productVariant = $orderItem->getVariant();

        $priceData = [
            'unit_amount' => $orderItem->getTotal(),
            'currency' => $order->getCurrencyCode(),
            'product_data' => [
                'name' => $this->lineItemNameProvider->getItemName($orderItem),
                'images' => $this->lineItemImagesProvider->getImageUrls($orderItem),
            ],
        ];
        
        // s'il existe DurationValue alors on ajoute recurring pour l'item
        if ($orderItem->getVariant()->getDurationValue() !== null) {
            // $intervalCount = $orderItem->getVariant()->getDurationValue() > 1 ? $orderItem->getVariant()->getDurationValue() : 1;

            $priceData['recurring'] = [
                // Supprime le 's' a la fin
                'interval' => rtrim($orderItem->getVariant()->getDurationUnit(), 's'),
                'interval_count' => 1,
                
            ];
        }

        return [
            'price_data' => $priceData,
            'quantity' => 1,
        ];
    }
}
