<?php

declare(strict_types=1);

namespace App\ApiResource\User\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\User\Dto\ApiUserDto;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiUserProvider implements ProviderInterface
{
    public function __construct(private UserServiceInterface $userService) {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!isset($uriVariables['userId']) || !$uriVariables['userId']) {
            throw new BadRequestHttpException("User ID not set.");
        }
        if (!is_numeric($uriVariables['userId'])) {
            throw new BadRequestHttpException("User ID not numeric.");
        }

        $userId = (int) $uriVariables['userId'];
        $user = $this->userService->findById($userId);
        if (!$user) {
            return null;
        }
        return ApiUserDto::makeFromUser($user);
    }
}
