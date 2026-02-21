<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PaymentStatusUpdateMessage;
use App\Services\Orders\Service\OrderService;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\Payment\Service\PaymentService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class PaymentStatusUpdateMessageHandler
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentService $paymentService,
    ) {
    }
    public function __invoke(PaymentStatusUpdateMessage $message) {

       $content = json_decode($message->getContent(), true);
       $payment = $this->paymentService->getPaymentByReference($content['paymentReference']);
       if ($payment !== null) {
           $order = $payment->getOrder();
           if ($order->getOrderPaymentStatus()->getHandle() === PaymentStatus::PAID_STATUS_HANDLE) {
               $order->markOrderPaid();
               $this->orderService->saveOrder($order);
           }
       }
    }
}
