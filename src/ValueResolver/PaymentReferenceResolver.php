<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Service\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class PaymentReferenceResolver implements ValueResolverInterface
{
    public function __construct(private PaymentServiceInterface $paymentService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {

        $argumentType = $argument->getType();
        if ($argumentType !== Payment::class) {
            return [];
        }


        $paymentReference = $request->attributes->get('paymentReference');


        if (!is_string($paymentReference)) {
            return [];
        }


        $order = $this->paymentService->getPaymentByReference($paymentReference);
        return $order ? [$order] : [];
    }
}
