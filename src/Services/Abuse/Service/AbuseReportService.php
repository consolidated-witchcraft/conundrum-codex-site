<?php

declare(strict_types=1);

namespace App\Services\Abuse\Service;

use App\Services\Abuse\Domain\AbuseReport;
use App\Services\Abuse\Infrastructure\AbuseReportRepository;
use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

class AbuseReportService extends AbstractDomainEntityService implements DomainEntityServiceInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly AbuseReportRepository $abuseReportRepository
    ) {
        parent::__construct($entityManager);
    }

    /**
     * @return AbuseReport[]
     */
    public function findUnactioned(): array
    {
        return $this->abuseReportRepository->findUnactioned();
    }

    /**
     * @return AbuseReport[]
     */
    public function getAbuseReportsForReportedUser(User $user, bool $checkedOnly = true): array {
        return $this->abuseReportRepository->getAbuseReportsForReportedUser($user, $checkedOnly);
    }

    /**
     * @return AbuseReport[]
     */
    public function getAbuseReportsByUser(User $user): array {

        return $this->abuseReportRepository->getAbuseReportsByUser($user);
    }

    public function deleteReport(AbuseReport $report): void {
        $this->entityManager->remove($report);
    }

    public function saveReport(AbuseReport $report): AbuseReport {
        $this->entityManager->persist($report);
        $this->entityManager->flush();
        return $report;
    }
}
