<?php

declare(strict_types=1);

namespace App\Services\BugReport\Service;

use App\Services\BugReport\Domain\BugReport;
use App\Services\BugReport\Infrastructure\BugReportRepository;
use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class BugReportService extends AbstractDomainEntityService implements DomainEntityServiceInterface
{
    public function __construct(
        private BugReportRepository $bugReportRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    /**
     * @return BugReport[]
     */
    public function findUnactioned(): array {
        return $this->bugReportRepository->findUnactioned();
    }

    public function save(BugReport $bugReport, bool $flush = true): BugReport {
        $this->entityManager->persist($bugReport);
        if ($flush) {
            $this->flush();
        }

        return $bugReport;
    }
}
