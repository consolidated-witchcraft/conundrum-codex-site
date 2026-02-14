<?php

declare(strict_types=1);

namespace App\Services\Tax\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderTax;
use App\Services\Tax\Domain\Interfaces\TaxRateProviderInterface;
use App\Services\Tax\Service\Interfaces\TaxServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class TaxService extends AbstractDomainEntityService implements TaxServiceInterface
{
    /**
     * @param iterable<TaxRateProviderInterface> $taxRateProviders
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        #[AutowireIterator('app.tax.provider')]
        private iterable $taxRateProviders
    ) {
        parent::__construct($entityManager);
    }

    public function generateTaxesForOrder(Order $order): Collection
    {
        $orderTaxes = new ArrayCollection();

        foreach ($this->taxRateProviders as $taxRateProvider) {
            $totalTax = 0;
            foreach ($order->getProductOrderLines() as $productOrderLine) {
                if (!$productOrderLine->getProduct()->isTaxExempt()) {
                    $totalTax += $taxRateProvider->getTaxCentessimal($productOrderLine->getProduct()->getPreTaxPriceCentessimal(), $productOrderLine->getQuantity());
                }
            }
            foreach ($order->getChargeLines() as $chargeLine) {
                $totalTax += $taxRateProvider->getTaxCentessimal($chargeLine->getPriceCentessimal(), 1);
            }
            $orderTaxes->add(
                new OrderTax(
                    taxRatePublicReference: $taxRateProvider->getTaxRateName(),
                    taxRateName: $taxRateProvider->getTaxRateName(),
                    taxRateHandle: $taxRateProvider->getTaxRateHandle(),
                    order: $order,
                    amountCentessimal: $totalTax,
                )
            );
        }

        return $orderTaxes;
    }
}
