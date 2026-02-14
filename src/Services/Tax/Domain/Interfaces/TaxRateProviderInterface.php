<?php

namespace App\Services\Tax\Domain\Interfaces;

interface TaxRateProviderInterface
{
    public function getTaxRateName(): string;

    public function getTaxRateHandle(): string;

    public function getTaxCentessimal(int $preTaxPrice, int $quantity): int;
}
