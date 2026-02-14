<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Events\Domain\Event;
use App\Services\Events\Service\EventServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class EventSlugResolver implements ValueResolverInterface
{
    public function __construct(private EventServiceInterface $eventService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== Event::class) {
            return [];
        }

        $slug = $request->attributes->get('eventSlug');

        if (!is_string($slug)) {
            return [];
        }

        $game = $this->eventService->findEventBySlug($slug);

        return $game ? [$game] : [];
    }
}
