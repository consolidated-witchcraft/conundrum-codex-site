<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameSession;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class GameSessionIdResolver implements ValueResolverInterface
{
    public function __construct(private GameServiceInterface $gameService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== GameSession::class) {
            return [];
        }

        $id = $request->attributes->get('gameSessionId');

        if (!is_numeric($id)) {
            return [];
        }

        $game = $this->gameService->findGameSessionById((int) $id);

        return $game ? [$game] : [];
    }
}
