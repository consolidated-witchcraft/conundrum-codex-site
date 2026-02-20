<?php

declare(strict_types=1);

namespace App\ApiResource\User\Providers;


use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\User\Dto\ApiUserCartProductLineOutputDto;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiUserCartProductLineProvider implements ProviderInterface
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly IriConverterInterface $iriConverter,
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
        if ($operation instanceof Post) {
            return null;
        }

        if ($operation instanceof GetCollection) {
            $productCartLines = array_map(
                fn ($line): ?string => $this->iriConverter->getIriFromResource(
                    ApiUserCartProductLineOutputDto::class,
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
            return $productCartLines;
        }

        /**
         * There operations require a ProductLineId
         */
        if (
            $operation instanceof Get ||
            $operation instanceof Delete ||
            $operation instanceof Patch
        ) {
            if (!isset($uriVariables['productLineId']) || !$uriVariables['productLineId']) {
                throw new BadRequestHttpException("Product Line ID not set.");
            }
            if (!is_numeric($uriVariables['productLineId'])) {
                throw new BadRequestHttpException("Product Line ID not numeric.");
            }


            $productLineId = (int) $uriVariables['productLineId'];
            $productLine = $cart->getUserCartProductLineById($productLineId);
            if (!$productLine) {
                return null;
            }
            return ApiUserCartProductLineOutputDto::makeFromCartProductLine($productLine);
        }
    }
}
