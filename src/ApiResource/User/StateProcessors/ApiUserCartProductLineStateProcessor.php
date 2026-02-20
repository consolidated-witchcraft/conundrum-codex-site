<?php

declare(strict_types=1);

namespace App\ApiResource\User\StateProcessors;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\User\Dto\ApiUserCartProductLineDto;
use App\Services\User\Service\Interfaces\UserServiceInterface;

/**
 * @implements ProcessorInterface<ApiUserCartProductLineDto, ApiUserCartProductLineDto|void>
 */
class ApiUserCartProductLineStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $isZeroingLine = false;
        if ($operation instanceof Patch) {

            $user = $this->userService->findById($data->userId);
            $cart = $user->getUserCart();
            $productLine = $cart->getUserCartProductLineById($data->id);
            if ($data->quantity === 0) {
                $isZeroingLine = true;

                $cart->removeUserCartProductLine($productLine);
            } else {
                $productLine->setQuantity($data->quantity);
            }
            $this->userService->saveUser($user);
        }

        return $isZeroingLine ? null : $data;
    }
}
