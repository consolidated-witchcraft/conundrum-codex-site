<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PaymentStatusUpdateMessage;
use App\Services\Orders\Service\OrderService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class PaymentStatusUpdateMessageHandler
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }
    public function __invoke(PaymentStatusUpdateMessage $message) {

       $content = json_decode($message->getContent(), true);
       dd($content);
    }
}
