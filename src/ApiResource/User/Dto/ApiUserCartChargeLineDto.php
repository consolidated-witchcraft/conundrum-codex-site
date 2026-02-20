<?php

declare(strict_types=1);
namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleInstance\Static\Casebook\Dto\ApiCasebookDtoApi;
use App\ApiResource\User\Providers\ApiUserCartProvider;
use App\Services\Quotation\Domain\Quotation;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCartCharge;
use App\Services\User\Domain\UserCart\UserCartProductLine;


class ApiUserCartChargeLineDto
{
    private function __construct(
        public readonly string $chargeTypeHandle,
        public readonly string $chargeTypeName,
        public readonly int $preTaxAmountCentessimal
    ) {}

    public static function makeFromCartChargeLine(UserCartCharge $userCartCharge): ApiUserCartChargeLineDto {
        return new static(
            chargeTypeHandle: $userCartCharge->getChargeType()->getHandle(),
            chargeTypeName: $userCartCharge->getChargeType()->getName(),
            preTaxAmountCentessimal: $userCartCharge->getPreTaxAmountCentessimal(),
        );
    }
}
