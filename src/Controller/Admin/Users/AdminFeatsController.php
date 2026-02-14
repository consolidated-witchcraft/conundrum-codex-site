<?php

declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Controller\AbstractBaseController;
use App\Dto\Admin\User\AdminFeatDto;
use App\Form\Admin\Users\FeatType;
use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminFeatsController extends AbstractBaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserService            $userService,
    ) {
    }

    #[Route('admin/users/feats', name: 'admin.users.feats')]
    #[IsGranted('manage_feats')]
    public function index(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Manage Feats',
            'feats' => $this->userService->findAllFeats(),
        ];
        return $this->render('admin/users/feats/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/feats/{featHandle}/edit', name: 'admin.users.feats.edit')]
    #[IsGranted('manage_feats')]
    public function edit(
        UserFeat $feat,
        Request $request
    ): Response {
        $dto = AdminFeatDto::makeFromFeat($feat);
        $form = $this->createForm(FeatType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feat->setDescription($dto->description);
            $feat->setGamesMasterAwardable($dto->gamesMasterAwardable);
            $feat->setLabel($dto->label);
            $feat->setRarity($dto->rarity);
            $feat->setIconClass($dto->iconClass);
            $this->entityManager->persist($feat);
            $this->entityManager->flush();
            $this->addFlash('success', 'Changes saved successfully');
            return $this->redirectToRoute('admin.users.feats');
        }

        $pageVars = [
            'pageTitle' => sprintf("Edit feat %s", $feat->getLabel()),
            'feat' => $feat,
            'form' => $form
        ];
        return $this->render('admin/users/feats/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    /**
     * @throws InvalidHandleException
     */
    #[Route('admin/users/feats/add', name: 'admin.users.feats.add')]
    #[IsGranted('manage_feats')]
    public function add(
        Request $request
    ): Response {
        $dto = new AdminFeatDto();
        $form = $this->createForm(FeatType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feat = new UserFeat(
                label: $dto->label,
                handle: $dto->handle,
                description: $dto->description,
                iconClass: $dto->iconClass,
                rarity: $dto->rarity,
                gamesMasterAwardable: $dto->gamesMasterAwardable,
            );
            $this->entityManager->persist($feat);
            $this->entityManager->flush();
            $this->addFlash('success', 'Feat added successfully');
            return $this->redirectToRoute('admin.users.feats');
        }

        $pageVars = [
            'pageTitle' => "Add a new Feat",
            'form' => $form
        ];
        return $this->render('admin/users/feats/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
