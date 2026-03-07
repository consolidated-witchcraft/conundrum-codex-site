<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Worlds;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Worlds\UserWorldCreationDto;
use App\Form\Visitor\Worlds\WorldCreateType;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\User\Domain\User;
use App\Services\World\Domain\World;
use App\Services\World\Service\Interfaces\WorldServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorldsController extends AbstractBaseController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly WorldServiceInterface $worldService,
        FeatureSwitcherServiceInterface $featureSwitcherService
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[Route('/worlds', name: 'app.worlds.index')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $pageVars = [
            'pageTitle' => 'My Worlds',
            'user' => $user,
        ];
        return $this->render('visitor/worlds/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/worlds/new', name: 'app.worlds.create')]
    public function addWorld(Request $request): Response {
        $dto = new UserWorldCreationDto();
        $form = $this->createForm(WorldCreateType::class, $dto);
        $form->handleRequest($request);
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $world = new World(
                name: $dto->name,
                description: $dto->description,
                slug: $dto->slug,
                creator: $user
            );
            $success = true;
            $violations = $this->validator->validate($world);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $success = false;
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->worldService->saveWorld($world);
                $this->addFlash('success', 'World created.');
                return $this->redirectToRoute('app.worlds.index');
            }
        }
        $pageVars = [
            'pageTitle' => 'Create a new World',
            'form' => $form
        ];
        return $this->render('visitor/worlds/add.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
