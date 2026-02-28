<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PaymentStatusUpdateMessage;
use App\Services\Orders\Domain\Exceptions\InvalidOrderStatusChangeRequestedException;
use App\Services\Orders\Domain\Exceptions\UnknownOrderStatusException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Service\OrderService;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\Payment\Service\Interfaces\PaymentServiceInterface;
use App\Services\Payment\Service\PaymentService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class PaymentStatusUpdateMessageHandler
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentServiceInterface $paymentService,
    ) {
    }

    /**
     * @throws UnknownOrderStatusException
     * @throws UnknownPaymentStatusException
     * @throws InvalidOrderStatusChangeRequestedException
     * @throws \JsonException
     */
    public function __invoke(PaymentStatusUpdateMessage $message): void
    {

       $content = json_decode($message->getContent(), true);

       $oldPaymentStatusHandle = $content['oldPaymentStatus'];
       $newPaymentStatusHandle = $content['newPaymentStatus'];

       if ($oldPaymentStatusHandle !== $newPaymentStatusHandle) {
           $payment = $this->paymentService->getPaymentByReference($content['paymentReference']);
           if ($payment !== null) {
               $order = $payment->getOrder();

               if ($newPaymentStatusHandle === PaymentStatus::PAID_STATUS_HANDLE) {
                   $this->paymentService->generateReceiptForPayment($payment);
               }
               if ($order->getOrderPaymentStatus()->getHandle() === PaymentStatus::PAID_STATUS_HANDLE) {
                   $order->markOrderPaid();
                   $this->orderService->saveOrder($order);
               }
           }
       }
    }
}
