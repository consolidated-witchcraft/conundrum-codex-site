<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Message\OrderStatusUpdateMessage;
use App\Message\PaymentStatusUpdateMessage;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Order::class)]
class OrderStatusChangeSubscriber
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function postUpdate(Order $order, PostUpdateEventArgs $event): void
    {
        $changeSet = $event->getObjectManager()->getUnitOfWork()->getEntityChangeSet($order);
        //We're not changing the payment status. Fuggedaboutit.
        if (
            !isset($changeSet['orderStatus'])
            || count($changeSet['orderStatus']) < 2
        ) {
            return;
        }
        $initialStatus = reset($changeSet['orderStatus']);
        $lastStatus = end($changeSet['orderStatus']);
        if (!$initialStatus instanceof OrderStatus || ! $lastStatus instanceof OrderStatus) {
            return;
        }
        if ($initialStatus->getHandle() !== $lastStatus->getHandle()) {
            $this->messageBus->dispatch(
                new OrderStatusUpdateMessage(
                    orderReference: $order->getOrderReference(),
                    oldStatus: $initialStatus->getHandle(),
                    newStatus: $lastStatus->getHandle(),
                )
            );
        }
    }
}
