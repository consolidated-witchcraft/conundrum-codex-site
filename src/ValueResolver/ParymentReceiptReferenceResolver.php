<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\PaymentReceipt;
use App\Services\Payment\Service\Interfaces\PaymentServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class ParymentReceiptReferenceResolver implements ValueResolverInterface
{
    public function __construct(private PaymentServiceInterface $paymentService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {

        $argumentType = $argument->getType();
        if ($argumentType !== PaymentReceipt::class) {
            return [];
        }


        $paymentReceiptReference = $request->attributes->get('receiptReference');


        if (!is_string($paymentReceiptReference)) {
            return [];
        }


        $receipt = $this->paymentService->getReceiptByReference($paymentReceiptReference);

        return $receipt ? [$receipt] : [];
    }
}
