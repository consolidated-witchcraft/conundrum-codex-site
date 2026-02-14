<?php

namespace App\Services\Tax\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderTax;
use Doctrine\Common\Collections\Collection;

interface TaxServiceInterface extends DomainEntityServiceInterface
{
    /**
     * @param Order $order
     * @return Collection<int, OrderTax>
     */
    public function generateTaxesForOrder(Order $order): Collection;
}
