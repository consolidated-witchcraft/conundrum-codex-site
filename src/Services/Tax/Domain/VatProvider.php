<?php

declare(strict_types=1);

namespace App\Services\Tax\Domain;

use App\Services\Tax\Domain\Interfaces\TaxRateProviderInterface;

class VatProvider implements TaxRateProviderInterface
{

    private const string NAME = 'V.A.T';

    private const string HANDLE = 'vat';

    private const float PERCENTAGE_AS_DECIMAL = 0.2;
    public function getTaxRateName(): string
    {
        return self::NAME;
    }

    public function getTaxRateHandle(): string
    {
        return self::HANDLE;
    }

    public function getTaxCentessimal(int $preTaxPrice, int $quantity): int
    {
        $taxTotal = 0;

        for ($i = 0; $i < $quantity; $i++) {
            $taxTotal += $preTaxPrice * self::PERCENTAGE_AS_DECIMAL;
        }



        return (int) $taxTotal;
    }
}
