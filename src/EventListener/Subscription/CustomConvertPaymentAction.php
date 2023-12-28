<?php

declare(strict_types=1);

namespace App\EventListener\Subscription;

use Payum\Core\Request\Convert;
use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentAction as ConvertPaymentAction;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProviderInterface as DetailsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentActionInterface;

final class CustomConvertPaymentAction implements ConvertPaymentActionInterface
{
    /** @var DetailsProviderInterface */
    private $detailsProvider;

    private $decoratedConvertPaymentAction;

    public function __construct(DetailsProviderInterface $detailsProvider)
    {
        $this->detailsProvider = $detailsProvider;
        // $this->decoratedConvertPaymentAction = $decoratedConvertPaymentAction;
    }

    /** @param Convert $request */
    public function execute($request): void
    {
        // $detailsProvider = new DetailsProviderInterface();
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $details = $this->detailsProvider->getDetails($order);

        // dd($details);

        $request->setResult($details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array'
        ;
    }
}
