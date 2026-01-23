<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClue;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectClueRepository;
use App\Services\User\Domain\User;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use App\Services\User\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class UserResolver implements ValueResolverInterface
{
    public function __construct(private readonly UserServiceInterface $userService,)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== User::class) {
            return [];
        }

        $id = $request->attributes->get('userId');
        if (!is_string($id) && !is_int($id)) {
            return [];
        }

        $user = $this->userService->findById((int) $id);

        return $user ? [$user] : [];
    }
}
