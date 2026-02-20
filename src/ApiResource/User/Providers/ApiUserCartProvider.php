<?php

declare(strict_types=1);

namespace App\ApiResource\User\Providers;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\User\Dto\ApiUserCartDto;
use App\ApiResource\User\Dto\ApiUserCartProductLineDto;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiUserCartProvider implements ProviderInterface
{
    public function __construct(
        private UserServiceInterface $userService,
        private IriConverterInterface $iriConverter
    ) {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {


        if (!isset($uriVariables['cartId']) || !$uriVariables['cartId']) {
            throw new BadRequestHttpException("Cart ID not set.");
        }
        if (!is_numeric($uriVariables['cartId'])) {
            throw new BadRequestHttpException("Cart ID not numeric.");
        }

        $cartId = (int) $uriVariables['cartId'];

        $cart = $this->userService->findCartById($cartId);
        if (!$cart) {
            return null;
        }
        $cartDto =  ApiUserCartDto::makeFromCart($cart);
        $cartDto->cartProductLines = array_map(
            fn ($line): ?string => $this->iriConverter->getIriFromResource(
                ApiUserCartProductLineDto::class,
                context: [
                    'item_uri_template' => '/cart/{cartId}/productLines/{productLineId}',
                    'uri_variables' => [
                        'cartId' => (string) $cartId,
                        'productLineId' => (string) $line->getId(),
                    ],
                ]
            ),
            $cart->getUserCartProductLines()->toArray()
        );

        return $cartDto;
    }
}
