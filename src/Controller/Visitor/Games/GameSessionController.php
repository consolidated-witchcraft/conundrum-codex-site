<?php

namespace App\Controller\Visitor\Games;

use ApiPlatform\Validator\ValidatorInterface;
use App\Controller\AbstractBaseController;
use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\Visitor\Game\Sessions\GameSessionDto;
use App\Form\Visitor\Game\Sessions\AddSessionType;
use App\Security\GameManagerVoter;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\Files\Service\Interfaces\FilesEntityServiceInterface;
use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameSession;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use App\Services\User\Domain\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class GameSessionController extends AbstractBaseController
{
    use HandlesFileUploadsTrait;
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly GameEntityServiceInterface $gameService,
        protected SluggerInterface $slugger,
        protected readonly FilesEntityServiceInterface $filesService,
        #[Autowire('%kernel.project_dir%/public/uploads/images/games/sessions/')] private string $publicImagesDirectory,
        FeatureSwitcherServiceInterface $featureSwitcherService
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/sessions/add', name: 'app.games.sessions.add')]
    public function add(
        Game $game,
        Request $request
    ): Response
    {
        $dto = new GameSessionDto($game);
        $form = $this->createForm(AddSessionType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $image = null;
            if ($user instanceof User) {
                $imageData = $form->get('sessionImage')->getData();
                if ($imageData) {
                    $image = $this->filesService->uploadFile(
                        file: $imageData,
                        title: sprintf("Session image for %s", $dto->title),
                        description: sprintf("Session image for %s", $dto->title),
                        uploader: $user,
                        destination: $this->publicImagesDirectory,
                    );
                }
            }

            $gameSession = new GameSession(
                sessionDate: $dto->sessionDate,
                game: $game,
                title: $dto->title,
                sessionImage: $image,
                description: $dto->description,
            );
            $this->gameService->saveGameSession($gameSession);
            $this->addFlash('success', 'Game session added.');
            return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Add a session to %s", $game->getName()),
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => $game->getName(),
                    'active' => false
                ],
                [
                    'label' => 'Manage',
                    'active' => false
                ],
                [
                    'label' => 'Sessions',
                    'active' => false
                ],
                [
                    'label' => 'Add',
                    'active' => true
                ],
            ],
            'form' => $form,
            'game' => $game,
        ];
        return $this->render('/visitor/games/sessions/add.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/sessions/{gameSessionId}/edit', name: 'app.games.sessions.edit')]
    public function edit(
        Game $game,
        GameSession $gameSession,
        Request $request
    ): Response {
        $dto = GameSessionDto::makeFromGameSession($gameSession);
        $form = $this->createForm(AddSessionType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $image = null;
            if ($user instanceof User) {
                $imageData = $form->get('sessionImage')->getData();
                if ($imageData) {
                    $image = $this->filesService->uploadFile(
                        file: $imageData,
                        title: sprintf("Session image for %s", $dto->title),
                        description: sprintf("Session image for %s", $dto->title),
                        uploader: $user,
                        destination: $this->publicImagesDirectory,
                        fileToReplace: $gameSession->getSessionImage(),
                    );
                }
            }

            $gameSession->setSessionDate($dto->sessionDate);
            $gameSession->setTitle($dto->title);
            $gameSession->setDescription($dto->description);
            $gameSession->setSessionImage($image);
            if ($image) {
                $gameSession->setSessionImage($image);
            }

            $this->gameService->saveGameSession($gameSession);
            $this->addFlash('success', 'Game session edited.');
            return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Edit %s session %s", $game->getName(), $gameSession->getTitle()),
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => $game->getName(),
                    'active' => false
                ],
                [
                    'label' => 'Manage',
                    'active' => false
                ],
                [
                    'label' => 'Sessions',
                    'active' => false
                ],
                [
                    'label' => $gameSession->getTitle(),
                    'active' => false
                ],
                [
                    'label' => 'Edit',
                    'active' => true
                ],
            ],
            'form' => $form,
            'game' => $game,
            'gameSession' => $gameSession,
        ];
        return $this->render('/visitor/games/sessions/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('games/{gameSlug}/sessions/{gameSessionId}', name: 'app.games.sessions.view')]
    public function view(
        Game $game,
        GameSession $gameSession,
        Request $request
    ): Response {
        $pageVars = [
            'pageTitle' => sprintf("%s - %s", $gameSession->getTitle(), $gameSession->getSessionDate()->format('H:i l, jS F, Y')),
            'heroImageUrl' => $gameSession->getSessionImage()?->getPath(),
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => $game->getName(),
                    'active' => false
                ],
                [
                    'label' => 'Manage',
                    'active' => false
                ],
                [
                    'label' => 'Sessions',
                    'active' => false
                ],
                [
                    'label' => $gameSession->getTitle(),
                    'active' => true
                ],
            ],
            'game' => $game,
            'gameSession' => $gameSession,
        ];
        return $this->render('/visitor/games/sessions/view.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
