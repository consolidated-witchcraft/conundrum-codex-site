<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Events\Domain\Organisation;
use App\Services\Events\Service\EventServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class OrganisationHandleResolver implements ValueResolverInterface
{
    public function __construct(private EventServiceInterface $eventService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== Organisation::class) {
            return [];
        }

        $handle = $request->attributes->get('organisationHandle');

        if (!is_string($handle)) {
            return [];
        }

        $organisation = $this->eventService->getOrganisationByHandle($handle);

        return $organisation ? [$organisation] : [];
    }
}
