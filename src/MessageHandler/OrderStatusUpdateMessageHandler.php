<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\OrderStatusUpdateMessage;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;
use App\Services\Orders\Service\OrderService;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\Payment\Service\PaymentService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class OrderStatusUpdateMessageHandler
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }
    public function __invoke(OrderStatusUpdateMessage $message) {

       $content = json_decode($message->getContent(), true);
       $order = $this->orderService->getOrderByReference($content['orderReference']);
       if ($order !== null) {

           $oldStatusHandle = $content['oldStatus'];
           $newStatusHandle = $content['newStatus'];
           if ($oldStatusHandle === $newStatusHandle) {
               return;
           }
           if ($newStatusHandle === OrderStatus::PAID_STATUS_HANDLE) {
                //Generate a receipt
               $this->orderService->generateReceiptForOrder($order);
           }
       }
           //Here we process reactions to an order status change.
           /*
           public const array KNOWN_ORDER_STATUSES = [
               self::INVOICED_STATUS_HANDLE => 'Invoiced',
               self::PAID_STATUS_HANDLE => 'Paid',
               self::SHIPPED_STATUS_HANDLE => 'Shipped',
               self::DELIVERED_STATUS_HANDLE => 'Delivered',
               self::REFUNDED_STATUS_HANDLE => 'Refunded',
               self::CANCELED_STATUS_HANDLE => 'Canceled',
               self::CLOSED_STATUS_HANDLE => 'Closed',
           ];*/
           /**
            * If the new order status is paid:
            * -- Generate a receipt
            * -- If the order contains physical products, generate a shipment.
            * -- If the order contains electronic tickets, generate them.
            *
            * If the new order status is cancelled:
            *   - If any payments have been made, generate a refund.
            *   - If any unshipped shipments have been generated, cancel them.
            *   - If any shipped shipments have been shipped, generate an RMA.
            */
    }
}
