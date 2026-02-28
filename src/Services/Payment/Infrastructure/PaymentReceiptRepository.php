<?php

declare(strict_types=1);

namespace App\Services\Payment\Infrastructure;

use App\Services\Payment\Domain\PaymentReceipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentReceipt>
 */
class PaymentReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentReceipt::class);
    }

    public function findByReceiptReference(string $receiptReference): ?PaymentReceipt {
        return $this->createQueryBuilder('pr')
            ->where('pr.receiptReference = :receiptReference')
            ->setParameter('receiptReference', $receiptReference)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
