<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Dto\Admin\Abuse\AbuseReportCheckDto;
use App\Form\Admin\AbuseReportCheckType;
use App\Services\Abuse\Domain\AbuseReport;
use App\Services\Abuse\Infrastructure\AbuseReportRepository;
use App\Services\Abuse\Service\AbuseReportService;
use App\Services\Quotation\Service\QuotationService;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminAbuseReportsController extends AbstractBaseController
{
    public function __construct(
        private readonly AbuseReportService $abuseReportService
    ){
    }

    #[Route('admin/abusereports/{id}/check', name: 'admin.abuse.check')]
    #[IsGranted('block_users')]
    public function index(
        AbuseReport $report,
        Request $request
    ): Response {

        $dto = new AbuseReportCheckDto($report);
        $form = $this->createForm(AbuseReportCheckType::class, $dto);
        $user = $this->getUser();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($dto->reportConfirmed === true) {
                $report->markConfirmed();
            }

            if (
                $dto->reportReporter === true &&
                $user instanceof User &&
                $report->getReportingUser() !== null
            ) {
                $dateNow = new \DateTimeImmutable();
                $reporterReport = new AbuseReport(
                    reportedUser: $report->getReportingUser(),
                    reason: 'Nuisance abuse report.',
                    notes: 'Reported in Admin panel',
                    checkedDate: $dateNow,
                    confirmedDate: $dateNow,
                    reportingUser: $user
                );
                $this->abuseReportService->saveReport($reporterReport);
            }
            $report->markChecked();
            $this->abuseReportService->saveReport($report);
            $this->addFlash('success', 'Report actioned successfully.');
            return $this->redirectToRoute('admin.dashboard');
        }


        $pageVars = [
            'pageTitle' => sprintf("Review report %d", $report->getId()),
            'report' => $report,
            'reporteeStrikes' => count($this->abuseReportService->getAbuseReportsForReportedUser($report->getReportedUser())),
            'form' => $form
        ];
        return $this->render('admin/abuse/check.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
