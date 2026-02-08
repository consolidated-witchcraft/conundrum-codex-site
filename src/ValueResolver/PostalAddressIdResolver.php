<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\PostalAddress\Service\PostalAddressServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class PostalAddressIdResolver implements ValueResolverInterface
{
    public function __construct(
        private PostalAddressServiceInterface $postalAddressService
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== PostalAddress::class) {
            return [];
        }

        $id = $request->attributes->get('postalAddressId');

        if (!is_numeric($id)) {
            return [];
        }

        $postalAddress = $this->postalAddressService->find((int) $id);

        return $postalAddress ? [$postalAddress] : [];
    }
}
