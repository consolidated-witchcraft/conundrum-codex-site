<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Payment;

use App\Controller\AbstractBaseController;
use App\Form\Visitor\DummyPaymentForm;
use App\Services\Orders\Domain\Exceptions\OrderPaymentInvalidStatusChangeException;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Service\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractBaseController
{

    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    ) {}

    /**
     * @throws OrderPaymentInvalidStatusChangeException
     */#[Route('orders/{orderReference}/payments/{paymentReference}/pay', name: 'payments.pay')]
    public function pay(
        Payment $payment,
        Order   $order,
        Request $request
    ) {
        $form = $this->createForm(DummyPaymentForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $payment->markProcessing();
            $payment->markPaid();
            $this->paymentService->savePayment($payment);
        }
        $pageVars = [
            'pageTitle' => 'Pay',
            'order' => $order,
            'payment' => $payment,
            'form' => $form
        ];
        return $this->render('visitor/checkout/pay.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
