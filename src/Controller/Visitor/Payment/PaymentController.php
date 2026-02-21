<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Payment;

use App\Controller\AbstractBaseController;
use App\Form\Visitor\DummyPaymentForm;
use App\Security\UserOwnsOrderVoter;
use App\Services\Orders\Domain\Exceptions\OrderPaymentInvalidStatusChangeException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Service\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PaymentController extends AbstractBaseController
{

    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    ) {}

    /**
     * @throws OrderPaymentInvalidStatusChangeException
     * @throws UnknownPaymentStatusException
     */
    #[IsGranted(UserOwnsOrderVoter::OPERATION, 'order')]
    #[Route('orders/{orderReference}/payments/{paymentReference}/pay', name: 'payments.pay')]
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
            $this->addFlash('success', "Thank you! Your payment was successful.");
            return $this->redirectToRoute('orders.view', ['orderReference' => $order->getOrderReference()]);
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
