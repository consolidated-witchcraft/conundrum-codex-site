<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Events\Service\EventServiceInterface;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Service\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class OrderReferenceResolver implements ValueResolverInterface
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {

        $argumentType = $argument->getType();
        if ($argumentType !== Order::class) {
            return [];
        }


        $orderReference = $request->attributes->get('orderReference');


        if (!is_string($orderReference)) {
            return [];
        }


        $order = $this->orderService->getOrderByReference($orderReference);
        return $order ? [$order] : [];
    }
}
