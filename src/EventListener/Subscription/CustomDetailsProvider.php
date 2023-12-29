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
        // $lineItems = $this->lineItemsProvider->getLineItems($order);



$stripe = new \Stripe\StripeClient($_ENV['PAYUM_STRIPE_CHECKOUT_SECRET_KEY']);
// Créer un abonnement mensuel
$subscription1 = $stripe->subscriptions->create([
    'customer' => 'cus_PGtraHz4pPNYHJ', // Remplacez par l'ID du client réel
    'items' => [
        [
            'price_data' => [
                'unit_amount' => 7999,
                'currency' => 'EUR',
                'product' => 'prod_PGsZ9XHX754qA3',
                'recurring' => [
                    'interval' => 'year',
                    'interval_count' => 1,
                ],
            ],
            'quantity' => 1,
        ],
    ],
]);

// Créer un abonnement tous les 6 mois
$subscription2 = $stripe->subscriptions->create([
    'customer' => 'cus_PGtraHz4pPNYHJ', // Remplacez par l'ID du client réel
    'items' => [
        [
            'price_data' => [
                'unit_amount' => 4999,
                'currency' => 'EUR',
                'product' => 'prod_PGsZ9XHX754qA3',
                'recurring' => [
                    'interval' => 'month',
                    'interval_count' => 6,
                ],
            ],
            'quantity' => 1,
        ],
    ],
]);         

$details['line_items'] = [
    [

        'price' => $subscription1->items->data[0]->price->id,
        'quantity' => 1,
    ],
    [

        'price' => $subscription2->items->data[0]->price->id,
        'quantity' => 1,
    ],

];

$lineItems =$details['line_items'];


        if (null !== $lineItems) {
            $details['line_items'] = $lineItems;
        }


        // s'il existe un item avec 'recurring' alors le mode = subscription 
        // foreach ($lineItems as $lineItem){
        //     if(isset($lineItem['price_data']['recurring'])){
        //         $details['mode'] = 'subscription';
        //         break;
        //     }
        // }
        dd($lineItems);

        $paymentMethodTypes = $this->paymentMethodTypesProvider->getPaymentMethodTypes($order);
        if ([] !== $paymentMethodTypes) {
            $details['payment_method_types'] = $paymentMethodTypes;
        }
        
        return $details;
    }

}
