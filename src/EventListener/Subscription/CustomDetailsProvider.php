<?php

declare(strict_types=1);

namespace App\EventListener\Subscription;

use Sylius\Component\Core\Model\OrderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\ModeProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\CustomerEmailProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProvider as DetailsProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\PaymentMethodTypesProviderInterface;

final class CustomDetailsProvider implements DetailsProviderInterface
{
    /** @var CustomerEmailProviderInterface */
    private $customerEmailProvider;

    /** @var LineItemsProviderInterface */
    private $lineItemsProvider;

    /** @var PaymentMethodTypesProviderInterface */
    private $paymentMethodTypesProvider;

    /** @var ModeProviderInterface */
    private $modeProvider;

    private $decoratedDetailsProvider;

    public function __construct(
        CustomerEmailProviderInterface $customerEmailProvider,
        LineItemsProviderInterface $lineItemsProvider,
        PaymentMethodTypesProviderInterface $paymentMethodTypesProvider,
        ModeProviderInterface $modeProvider,
        // DetailsProvider $decoratedDetailsProvider

    ) {
        $this->customerEmailProvider = $customerEmailProvider;
        $this->lineItemsProvider = $lineItemsProvider;
        $this->paymentMethodTypesProvider = $paymentMethodTypesProvider;
        $this->modeProvider = $modeProvider;
        // $this->decoratedDetailsProvider = $decoratedDetailsProvider;
    }

    public function getDetails(OrderInterface $order): array
    {
        $details = [];

        $customerEmail = $this->customerEmailProvider->getCustomerEmail($order);
        if (null !== $customerEmail) {
            $details['customer_email'] = $customerEmail;
        }

        $details['mode'] = $this->modeProvider->getMode($order);
        $lineItems = $this->lineItemsProvider->getLineItems($order);




// $lineItems = [
//     [
//         'price_data' => [
//             'unit_amount' => 7999,
//             'currency' => 'EUR',
//             'product_data' => [
//                 'name' => 'VisioCare myBuddy Consult',
//                 'images' => ['https://example.com/product1-image.jpg'],
//             ],
//         ],
//         'quantity' => 1,
//     ],
//     [
//         'price_data' => [
//             'unit_amount' => 14999,
//             'currency' => 'EUR',
//             'product_data' => [
//                 'name' => 'VisioCare myBuddy',
//                 'images' => ['https://example.com/product2-image.jpg'],
//             ],
//             'recurring' => [
//                 'interval' => 'month',
//                 'interval_count' => 6,
//             ],
//         ],
//         'quantity' => 1,
//     ],
//     [
//         'price_data' => [
//             'unit_amount' => 44999,
//             'currency' => 'EUR',
//             'product_data' => [
//                 'name' => 'VisioCare Home',
//                 'images' => ['https://example.com/product2-image.jpg'],
//             ],
//             'recurring' => [
//                 'interval' => 'month',
//                 'interval_count' => 12,
//             ],
//         ],
//         'quantity' => 1,
//     ],
// ];


        if (null !== $lineItems) {
            $details['line_items'] = $lineItems;
        }


        // s'il existe un item avec 'recurring' alors mode = subscription 
        foreach ($lineItems as $lineItem){
            if(isset($lineItem['price_data']['recurring'])){
                $details['mode'] = 'subscription';
                break;
            }
        }

        // dd($lineItems);

        $paymentMethodTypes = $this->paymentMethodTypesProvider->getPaymentMethodTypes($order);
        if ([] !== $paymentMethodTypes) {
            $details['payment_method_types'] = $paymentMethodTypes;
        }
        
        return $details;
    }

}
