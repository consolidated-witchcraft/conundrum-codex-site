<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Services\Abuse\Infrastructure\AbuseReportRepository;
use App\Services\Abuse\Service\AbuseReportService;
use App\Services\BugReport\Infrastructure\BugReportRepository;
use App\Services\BugReport\Service\BugReportService;
use App\Services\Quotation\Service\QuotationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractBaseController
{

    public function __construct(
        private AbuseReportService $abusereportService,
        private BugReportService   $bugReportService,
    ) {
    }

    #[Route('admin', name: 'admin.dashboard')]
    #[IsGranted('admin_dash')]
    public function dashboard(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Dashboard',
            'abuseReports' => $this->abusereportService->findUnactioned(),
            'bugReports' => $this->bugReportService->findUnactioned(),
        ];
        return $this->render('admin/dashboard/dashboard.html.twig', $this->populatePageVars($pageVars, $request));
    }


}
