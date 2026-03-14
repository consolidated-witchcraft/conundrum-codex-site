<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\User\Service\Interfaces\UserServiceInterface;
use App\Services\World\Domain\World;
use App\Services\World\Service\Interfaces\WorldServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class WorldResolver implements ValueResolverInterface
{
    public function __construct(private readonly WorldServiceInterface $worldService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== World::class) {
            return [];
        }

        $worldSlug = $request->attributes->get('worldSlug');
        if (!is_string($worldSlug)) {
            return [];
        }

        $user = $this->worldService->findBySlug($worldSlug);

        return $user ? [$user] : [];
    }
}
