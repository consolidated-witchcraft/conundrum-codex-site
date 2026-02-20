<?php

declare(strict_types=1);

namespace App\ApiResource\User\Providers;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\User\Dto\ApiUserCartDto;
use App\ApiResource\User\Dto\ApiUserCartProductLineDto;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiUserCartProductLineProvider implements ProviderInterface
{
    public function __construct(private UserServiceInterface $userService) {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!isset($uriVariables['cartId']) || !$uriVariables['cartId']) {
            throw new BadRequestHttpException("Cart ID not set.");
        }
        if (!is_numeric($uriVariables['cartId'])) {
            throw new BadRequestHttpException("Cart ID not numeric.");
        }

        if (!isset($uriVariables['productLineId']) || !$uriVariables['productLineId']) {
            throw new BadRequestHttpException("Product Line ID not set.");
        }
        if (!is_numeric($uriVariables['productLineId'])) {
            throw new BadRequestHttpException("Product Line ID not numeric.");
        }

        $cartId = (int) $uriVariables['cartId'];
        $productLineId = (int) $uriVariables['productLineId'];

        $cart = $this->userService->findCartById($cartId);
        if (!$cart) {
            return null;
        }
        $productLine = $cart->getUserCartProductLineById($productLineId);
        if (!$productLine) {
            return null;
        }
        return ApiUserCartProductLineDto::makeFromCartProductLine($productLine);
    }
}
