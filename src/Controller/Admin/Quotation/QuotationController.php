<?php

declare(strict_types=1);

namespace App\Controller\Admin\Quotation;

use App\Controller\AbstractBaseController;

use App\Dto\Admin\Quotation\QuotationDto;
use App\Form\Admin\AdminEntityDeleteType;
use App\Form\Admin\QuotationType;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\Quotation\Domain\Quotation;
use App\Services\Quotation\Service\QuotationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class QuotationController extends AbstractBaseController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private QuotationService $quotationService,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[Route('admin/quotations', name: 'admin.quotations.manage')]
    #[IsGranted('manage_quotations')]
    public function index(
        Request $request
    ): Response {

        $quotations = $this->quotationService->findAll();


        $pageVars = [
            'pageTitle' => 'Manage Quotations',
            'quotations' => $quotations

        ];
        return $this->render('admin/quotations/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/quotations/add', name: 'admin.quotations.add')]
    #[IsGranted('manage_quotations')]
    public function add(
        Request $request
    ): Response {

        $dto = new QuotationDto();
        $form = $this->createForm(QuotationType::class, $dto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quotation = new Quotation(
                quotation: $dto->quotation,
                citation: $dto->citation
            );
            $this->entityManager->persist($quotation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Quotation saved successfully');
            return $this->redirectToRoute('admin.quotations.manage');
        }

        $pageVars = [
            'pageTitle' => 'Add Quotation',
            'form' => $form

        ];
        return $this->render('admin/quotations/add.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/quotations/{id}/edit', name: 'admin.quotations.edit')]
    #[IsGranted('manage_quotations')]
    public function edit(
        Quotation $quotation,
        Request $request
    ): Response {

        $dto = new QuotationDto(
            quotation: $quotation->getQuotation(),
            citation: $quotation->getCitation()
        );
        $form = $this->createForm(QuotationType::class, $dto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quotation->setQuotation($dto->quotation);
            $quotation->setCitation($dto->citation);
            $this->entityManager->persist($quotation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Quotation saved successfully');
            return $this->redirectToRoute('admin.quotations.manage');
        }

        $pageVars = [
            'pageTitle' => sprintf("Edit quotation %d", $quotation->getId()),
            'form' => $form

        ];
        return $this->render('admin/quotations/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/quotations/{id}/delete', name: 'admin.quotations.delete')]
    #[IsGranted('manage_quotations')]
    public function delete(
        Quotation $quotation,
        Request $request
    ): Response {

        $form = $this->createForm(AdminEntityDeleteType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($quotation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Quotation Deleted');
            return $this->redirectToRoute('admin.quotations.manage');
        }

        $pageVars = [
            'pageTitle' => sprintf("Really delete quotation %d?", $quotation->getId()),
            'form' => $form

        ];
        return $this->render('admin/quotations/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
