<?php
namespace Payum\Stripe\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Security\SensitiveValue;
use Payum\Stripe\Request\Api\CreatePlan;

class ConvertPaymentAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = ArrayObject::ensureArrayObject($payment->getDetails());
        $details["amount"] = $payment->getTotalAmount();
        $details["currency"] = $payment->getCurrencyCode();
        $details["interval"] = 'month';
        $details["name"] = 'VisioCare myBud';
        $details["description"] = $payment->getDescription();

        if ($card = $payment->getCreditCard()) {
            if ($card->getToken()) {
                $details["customer"] = $card->getToken();
            } else {
                $details["card"] = SensitiveValue::ensureSensitive([
                    'number' => $card->getNumber(),
                    'exp_month' => $card->getExpireAt()->format('m'),
                    'exp_year' => $card->getExpireAt()->format('Y'),
                    'cvc' => $card->getSecurityCode(),
                ]);
            }
        }

        $request->setResult((array) $details);

    //    /** @var \Payum\Core\Payum $payum */
    //    $payum = $this->get('payum');
    //    $payum->getGateway('stripe_checkout')->execute(new CreatePlan($details));
    //$payum->getGateway('stripe_checkout')->execute(new CreatePlan($details));

       
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() == 'array'
        ;
    }
}
