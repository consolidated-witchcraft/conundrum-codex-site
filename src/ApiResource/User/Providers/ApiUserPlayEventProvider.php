<?php

declare(strict_types=1);

namespace App\ApiResource\User\Providers;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\User\Dto\ApiUserPlayEventOutputDto;
use App\Security\CurrentUserCanAccessPrivateUserDataVoter;
use App\Services\PlayEvent\Service\Interfaces\PlayEventServiceInterface;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class ApiUserPlayEventProvider implements ProviderInterface
{
    public function __construct(
        private PlayEventServiceInterface $playEventService,
        private Security $security,
        private UserServiceInterface $userService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userId = $this->getUserId(uriVariables: $uriVariables);
        $user = $this->userService->findById($userId);

        if ($user === null) {
            return null;
        }

        if (!$this->security->isGranted(CurrentUserCanAccessPrivateUserDataVoter::REQUESTED_PERMISSION, $user)) {
            throw new AccessDeniedHttpException('You may not access this user\'s private data.');
        }

        if ($operation instanceof GetCollection) {
            return array_map(
                static fn ($playEvent): ApiUserPlayEventOutputDto => ApiUserPlayEventOutputDto::makeFromPlayEvent($playEvent),
                $this->playEventService->findByUser(user: $user)->toArray()
            );
        }

        if ($operation instanceof Get) {
            if (!isset($uriVariables['playEventId']) || !is_numeric($uriVariables['playEventId'])) {
                throw new BadRequestHttpException('Play Event ID not numeric.');
            }

            $playEvent = $this->playEventService->find(id: (int) $uriVariables['playEventId']);

            if ($playEvent === null || $playEvent->getUser()->getId() !== $user->getId()) {
                return null;
            }

            return ApiUserPlayEventOutputDto::makeFromPlayEvent($playEvent);
        }

        return null;
    }

    private function getUserId(array $uriVariables): int
    {
        if (!isset($uriVariables['userId']) || !is_numeric($uriVariables['userId'])) {
            throw new BadRequestHttpException('User ID not numeric.');
        }

        return (int) $uriVariables['userId'];
    }
}
