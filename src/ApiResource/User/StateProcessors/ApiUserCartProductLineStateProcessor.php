<?php

declare(strict_types=1);

namespace App\ApiResource\User\StateProcessors;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\User\Dto\ApiUserCartProductLineOutputDto;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Service\Interfaces\ProductServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCartProductLine;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @implements ProcessorInterface<ApiUserCartProductLineOutputDto, ApiUserCartProductLineOutputDto|void>
 */
class ApiUserCartProductLineStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly ProductServiceInterface $productService,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!isset($uriVariables['cartId']) || !is_numeric($uriVariables['cartId'])) {
            throw new BadRequestHttpException('Cart ID not numeric.');
        }
        $requestedCartId = (int) $uriVariables['cartId'];


        $isDeletingLine = false;
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return null;
        }
        $cart = $user->getUserCart();
        if ($cart->getId() !== $requestedCartId) {
            throw new AccessDeniedHttpException('You do not own this cart.');
        }

        if (
            $operation instanceof Patch
            || $operation instanceof Delete
        ) {

            if (!isset($uriVariables['productLineId']) || !$uriVariables['productLineId']) {
                throw new BadRequestHttpException("Product Line ID not set.");
            }
            if (!is_numeric($uriVariables['productLineId'])) {
                throw new BadRequestHttpException("Product Line ID not numeric.");
            }


            $productLineId = (int) $uriVariables['productLineId'];


            $productLine = $cart->getUserCartProductLineById($productLineId);
            if ($operation instanceof Patch) {
                if ($data->quantity === 0) {
                    $isDeletingLine = true;

                    $cart->removeUserCartProductLine($productLine);
                } else {
                    $productLine->setQuantity($data->quantity);
                }
            }

            if ($operation instanceof Delete) {
                $isDeletingLine = true;
                $cart->removeUserCartProductLine($productLine);
            }
            $data->lineTotalCentessimal = $productLine->getPretaxTotalCentessimal();
            $this->userService->saveUser($user);
        }
        if ($operation instanceof Post) {
            $productVariantSku = $data->productVariantSku;
            $productVariant = $this->productService->getProductVariantBySku($productVariantSku);
            if (!$productVariant instanceof ProductVariant) {
                throw new BadRequestHttpException("No such SKU.");
            }

            $cart->addProductCartLine($productVariant, $data->quantity);
            $this->userService->saveUser($user);
        }


        return $isDeletingLine ? null : $data;
    }
}
