<?php

declare(strict_types=1);

namespace App\Controller\Admin\FeatureSwitcher;

use App\Controller\AbstractBaseController;
use App\Dto\Admin\Features\AdminFeatureSwitcherDto;
use App\Form\Admin\FeatureSwitcherToggleType;
use App\Form\Admin\NewFeatureSwitcherType;
use App\Services\Abuse\Service\AbuseReportService;
use App\Services\BugReport\Service\BugReportService;
use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminFeatureSwitcherController extends AbstractBaseController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[Route('admin/features', name: 'admin.features.manage')]
    #[IsGranted('manage_features')]
    public function manage(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Manage Features',
            'features' => $this->featureSwitcherService->getAllFeatureSwitchers()
        ];
        return $this->render('admin/features/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/features/{featureHandle}/toggle', name: 'admin.features.toggle')]
    #[IsGranted('manage_features')]
    public function toggle(FeatureSwitcher $featureSwitcher , Request $request): Response {
        $form = $this->createForm(FeatureSwitcherToggleType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()['enabled'] === true) {
                $featureSwitcher->enable();
            } else {
                $featureSwitcher->disable();
            }
            $this->featureSwitcherService->save($featureSwitcher);
            $this->addFlash('success', 'Feature switched successfully.');
            return $this->redirectToRoute('admin.features.manage');
        }
        $pageVars = [
            'pageTitle' => sprintf("Toggle feature %s", $featureSwitcher->getLabel()),
            'form' => $form
        ];
        return $this->render('admin/features/toggle.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/features/add', name: 'admin.features.add')]
    #[IsGranted('manage_features')]
    public function addFeature(Request $request): Response {
        $dto = new AdminFeatureSwitcherDto();

        $form = $this->createForm(NewFeatureSwitcherType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $featureSwitcher = new FeatureSwitcher(
                handle: $dto->handle,
                label: $dto->label,
            );
            if ($dto->enabled === true) {
                $featureSwitcher->enable();
            }
            $success = true;
            $violations = $this->validator->validate($featureSwitcher);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success === true) {
                $this->featureSwitcherService->save($featureSwitcher);
                $this->addFlash('success', 'Feature switched successfully.');
                return $this->redirectToRoute('admin.features.manage');
            }

        }
        $pageVars = [
            'pageTitle' => 'Create a new Feature',
            'form' => $form
        ];
        return $this->render('admin/features/add.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
