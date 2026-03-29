<?php

declare(strict_types=1);

namespace App\ApiResource\User\StateProcessors;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\User\Dto\ApiUserPlayEventInputDto;
use App\ApiResource\User\Dto\ApiUserPlayEventOutputDto;
use App\Security\CurrentUserCanAccessPrivateUserDataVoter;
use App\Services\PlayEvent\Domain\Enums\PlayEventTypeEnum;
use App\Services\PlayEvent\Service\Interfaces\PlayEventServiceInterface;
use App\Services\PuzzleInstance\Service\Interfaces\PuzzleInstanceEntityServiceInterface;
use App\Services\PuzzleTemplate\Service\Exceptions\TemplateNotFoundException;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @implements ProcessorInterface<ApiUserPlayEventInputDto, ApiUserPlayEventOutputDto|null>
 */
readonly class ApiUserPlayEventStateProcessor implements ProcessorInterface
{
    public function __construct(
        private PlayEventServiceInterface $playEventService,
        private PuzzleInstanceEntityServiceInterface $puzzleInstanceService,
        private Security $security,
        private UserServiceInterface $userService,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?ApiUserPlayEventOutputDto
    {
        if (!$data instanceof ApiUserPlayEventInputDto) {
            throw new BadRequestHttpException('Invalid play event payload.');
        }

        $userId = $this->getUserId(uriVariables: $uriVariables);
        $user = $this->userService->findById($userId);

        if ($user === null) {
            return null;
        }

        if (!$this->security->isGranted(CurrentUserCanAccessPrivateUserDataVoter::REQUESTED_PERMISSION, $user)) {
            throw new AccessDeniedHttpException('You may not access this user\'s private data.');
        }

        $type = PlayEventTypeEnum::tryFrom($data->type);
        if ($type === null) {
            throw new BadRequestHttpException('Invalid play event type.');
        }

        try {
            $puzzleInstance = $this->puzzleInstanceService->getInstanceByTemplateAndCode(
                templateSlug: $data->puzzleTemplateSlug,
                instanceCode: $data->puzzleInstanceCode
            );
        } catch (TemplateNotFoundException $exception) {
            throw new BadRequestHttpException('Unknown puzzle template.', previous: $exception);
        }

        if ($puzzleInstance === null) {
            throw new BadRequestHttpException('Unknown puzzle instance.');
        }

        $playEvent = $this->playEventService->record(
            user: $user,
            puzzleInstance: $puzzleInstance,
            type: $type
        );

        return ApiUserPlayEventOutputDto::makeFromPlayEvent($playEvent);
    }

    private function getUserId(array $uriVariables): int
    {
        if (!isset($uriVariables['userId']) || !is_numeric($uriVariables['userId'])) {
            throw new BadRequestHttpException('User ID not numeric.');
        }

        return (int) $uriVariables['userId'];
    }
}
