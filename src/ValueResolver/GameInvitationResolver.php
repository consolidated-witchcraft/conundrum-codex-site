<?php

namespace App\ValueResolver;


use App\Services\Game\Domain\GameInvitation;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class GameInvitationResolver implements ValueResolverInterface
{
    public function __construct(
        private GameEntityServiceInterface $gameService
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();
        if (!$argumentType === GameInvitation::class) {
            return [];
        }

        $code = $request->attributes->get('invitationCode');
        if (!is_string($code)) {
            return [];
        }

        $invitation = $this->gameService->findInvitationByCode($code);
        // create and return the value object
        return $invitation ? [$invitation] : [];
    }
}
