<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Payment;

use App\Controller\AbstractBaseController;
use App\Form\Visitor\DummyPaymentForm;
use App\Security\UserOwnsOrderVoter;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\Orders\Domain\Exceptions\OrderPaymentInvalidStatusChangeException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\PaymentReceipt;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\Payment\Service\Interfaces\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PaymentController extends AbstractBaseController
{

    public function __construct(
        private readonly PaymentServiceInterface $paymentService,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct($featureSwitcherService);
    }

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
    ): Response {
        $form = $this->createForm(DummyPaymentForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $newPaymentStatusHandle = $form->getData()['simulatedPaymentStatus'];
            switch ($newPaymentStatusHandle) {
                case PaymentStatus::UNPAID_STATUS_HANDLE:
                    $this->paymentService->markPaymentUnpaid($payment);
                    break;
                case PaymentStatus::PARTIALLY_PAID_STATUS_HANDLE:
                    break;
                case PaymentStatus::PROCESSING_STATUS_HANDLE:
                    $this->paymentService->markPaymentProcessing($payment);
                    break;
                case PaymentStatus::DECLINED_STATUS_HANDLE:
                    $this->paymentService->markPaymentDeclined($payment);
                    break;
                case PaymentStatus::PAID_STATUS_HANDLE:
                    $this->paymentService->markPaymentPaid($payment);
                    break;
                default:
                    throw new UnknownPaymentStatusException($newPaymentStatusHandle);
            }

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

    #[IsGranted(UserOwnsOrderVoter::OPERATION, 'order')]
    #[Route('orders/{orderReference}/payments/{paymentReference}/receipt/{receiptReference}', name: 'payments.receipt.view')]
    public function viewReceipt(
        Order $order,
        Payment $payment,
        PaymentReceipt $paymentReceipt,
    ): Response {
        return $this->render('printable/receipt.view.html.twig', [
            'documentTitle' => $paymentReceipt->getReceiptReference(),
            'receipt' => $paymentReceipt
        ]);
    }
}
