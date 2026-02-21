<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Message\PaymentStatusUpdateMessage;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Payment::class)]
class PaymentStatusChangeSubscriber
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function postUpdate(Payment $payment, PostUpdateEventArgs $event): void
    {
        $changeSet = $event->getObjectManager()->getUnitOfWork()->getEntityChangeSet($payment);
        //We're not changing the payment status. Fuggedaboutit.
        if (
            !isset($changeSet['paymentStatus'])
            || count($changeSet['paymentStatus']) < 2
        ) {
            return;
        }
        $initialStatus = reset($changeSet['paymentStatus']);
        $lastStatus = end($changeSet['paymentStatus']);
        if (!$initialStatus instanceof PaymentStatus || ! $lastStatus instanceof PaymentStatus) {
            return;
        }
        if ($initialStatus->getHandle() !== $lastStatus->getHandle()) {
            $this->messageBus->dispatch(
                new PaymentStatusUpdateMessage(
                    paymentReference: $payment->getPaymentReference(),
                    orderReference: $payment->getOrder()->getOrderReference(),
                    amountCentessimal: $payment->getPaymentAmountCentessimal(),
                    paymentName: $payment->getName(),
                    oldPaymentStatus: $initialStatus,
                    newPaymentStatus: $lastStatus,
                )
            );
        }
    }
}
