<?php

declare(strict_types=1);

namespace App\Controller;

use Webmozart\Assert\Assert;
use FOS\RestBundle\View\View;
use Payum\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
// use Sylius\Bundle\OrderBundle\Controller\OrderController as BaseOrderController;
use Sylius\Bundle\CoreBundle\Controller\OrderController as BaseOrderController;


class OrderController extends BaseOrderController
{

    public function thankYouAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $orderId = $request->getSession()->get('sylius_order_id', null);

        if (null === $orderId) {
            $options = $configuration->getParameters()->get('after_failure');

            return $this->redirectHandler->redirectToRoute(
                $configuration,
                $options['route'] ?? 'sylius_shop_homepage',
                $options['parameters'] ?? [],
            );
        }

        $request->getSession()->remove('sylius_order_id');
        $order = $this->repository->find($orderId);
        Assert::notNull($order);

        $payments = $order->getPayments();

        $paymentDetails = [];

        // Iterate over the payments collection
        /** @var PaymentInterface $payment */
        foreach ($payments as $payment) {
            // Add the payment details to the array
            $paymentDetails[] = [
                'id' => $payment->getId(),
                'amount' => $payment->getAmount(),
                'state' => $payment->getState(),
                'details' => $payment->getDetails(), //réponse de Stripe après un paiement accepter
            ];
        }

        // récupération des items 
        $orderItemUnits = $order->getItemUnits();

        $durationUnit = null;
        $durationValue = null;



        $subscriptionId = $paymentDetails[0]['details']['subscription'] ?? null;
        // s'il existe un id pour subscription dans la réponse stripe et paiement effectuer
        if ($subscriptionId !== null && $order->getPaymentState() === 'paid') {

            //boucle pour récup Value & Unit
            foreach ($orderItemUnits as $orderItemUnit) {
                // Accéder à l'objet OrderItem associé
                $orderItem = $orderItemUnit->getOrderItem()->getVariant();

                // Vérifier si les propriétés ne sont pas null
                if ($orderItem->getDurationValue() !== null && $orderItem->getDurationUnit() !== null) {
                    // Récupérer les valeurs durationValue et durationUnit
                    $durationValue = $orderItem->getDurationValue();
                    $durationUnit = rtrim($orderItem->getDurationUnit(), 's');
                    
                    break;
                }
            }

            if ($durationUnit !== null && $durationValue !== null) {
                // Utiliser $durationValue et $durationUnit pour MAJ la date de fin de l'abonnement
                $this->updateSubscription($payment, $durationValue, $durationUnit);
            }
        }


        return $this->render(
            $configuration->getParameters()->get('template'),
            [
                'order' => $order,

            ],
        );
    }

    /**
     * Permet de mettre une date de fin à l'abonnement
     */
    private function updateSubscription(Payment $payment, $durationValue, $durationUnit): void
    {
        $stripeClient = new \Stripe\StripeClient($_ENV['PAYUM_STRIPE_CHECKOUT_SECRET_KEY']);

        // Get subscription ID from payment details
        $subscriptionId = $payment->getDetails()['subscription'];

        // Récup le subscription sur Stripe
        $subscription = $stripeClient->subscriptions->retrieve($subscriptionId);

        // Récupére la date de création de la commande
        $orderCreatedAt = $payment->getOrder()->getCreatedAt();
        
        // Update subscription end date
        $newEndDate = strtotime('+' . $durationValue . ' ' . $durationUnit, $orderCreatedAt->getTimestamp());

        $stripeClient->subscriptions->update(
            $subscriptionId,
            ['cancel_at' => $newEndDate]
        );

    }

}