<?php

namespace App\Controller\Visitor\Games;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Controller\AbstractBaseController;
use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\Visitor\Game\GameDto;
use App\Form\Visitor\Game\ArchiveGameType;
use App\Form\Visitor\Game\GameType;
use App\Form\Visitor\Game\DeleteGameType;
use App\Form\Visitor\Game\UnArchiveGameType;
use App\Security\GameCreationVoter;
use App\Security\GameManagerVoter;
use App\Security\GamePlayerVoter;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\Game\Domain\Game;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use App\Services\User\Domain\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class GamesController extends AbstractBaseController
{
    use HandlesFileUploadsTrait;
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly GameServiceInterface $gameService,
        protected SluggerInterface $slugger,
        protected readonly FilesServiceInterface $filesService,
        #[Autowire('%kernel.project_dir%/public/uploads/images/games')] private string $publicImagesDirectory,
    ) {
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/games', name: 'app.games.index')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('login');
        }
        $pageVars = [
            'pageTitle' => 'My Games',
            'gamesMastered' => $user->getGamesMastered(),
            'gamesMember' => $user->getGames(),
            'archivedGames' => $user->getArchivedGamesMastered()
        ];
        return $this->render('/visitor/games/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameCreationVoter::CREATE_GAME_ACTION)]
    #[Route('/games/create', name: 'app.games.create')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to create a game.');
        }
        $dto = new GameDto();
        $form = $this->createForm(GameType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game = new Game(
                name: $dto->name,
                slug: $dto->slug,
                description: $dto->description,
                gamesMaster: $user,
            );
            $success = true;
            try {
                $this->validator->validate($game);
            } catch (ValidationException $e) {
                $violations = $e->getConstraintViolationList();
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
                $success = false;
            }

            if ($success) {
                $imageFile = $form->get('heroImageUrl')->getData();
                if ($imageFile) {
                    $heroImage = $this->handleFileUpload($imageFile, $this->publicImagesDirectory);
                    $game->setHeroImageFile($heroImage);
                }
                $this->gameService->saveGame($game);
                $this->addFlash('success', 'Game created successfully.');
                return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
            }

        }
        $pageVars = [
            'pageTitle' => 'Create a Game',
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => 'Create a game',
                    'active' => true
                ]
            ],
            'form' => $form
        ];
        return $this->render('/visitor/games/create.html.twig', $this->populatePageVars($pageVars, $request));
    }


    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/manage', name: 'app.games.manage')]
    public function manage(
        Game $game,
        Request $request
    ): Response
    {
        $title = sprintf('Manage %s', $game->getName());
        $invitations = $this->gameService->getOutstandingInvitationsForGame($game);
        $pageVars = [
            'pageTitle' => $title,
            'heroImageUrl' => $game->getHeroImageFile()?->getPath(),
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
                    'active' => true
                ],
            ],
            'game' => $game,
            'invitations' => $invitations
        ];
        return $this->render('/visitor/games/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/edit-info', name: 'app.games.edit')]
    public function editInfo(Game $game, Request $request): Response {
        $dto = GameDto::makeFromGame($game);
        $form = $this->createForm(GameType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('heroImageUrl')->getData();
            if ($imageFile) {
                $heroImage = $this->handleFileUpload($imageFile, $this->publicImagesDirectory);
                $game->setHeroImageFile($heroImage);
            }
            $game->setName($dto->name);
            $game->setDescription($dto->description);
            $this->gameService->saveGame($game);
            $this->addFlash('success', 'Game updated successfully.');
            return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Edit %s's basic information", $game->getName()),
            'form' => $form
        ];
        return $this->render('/visitor/games/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }


    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/archive', name: 'app.games.archive')]
    public function archive(
        Game $game,
        Request $request
    ): Response {
        $form = $this->createForm(ArchiveGameType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $game->archive();
            $this->gameService->saveGame($game);
            $this->addFlash('success', 'Game archived successfully.');
            return $this->redirectToRoute('app.games.index');

        }
        $pageVars = [
            'pageTitle' => sprintf("Archive %s", $game->getName()),
            'form' => $form
        ];
        return $this->render('/visitor/games/archive.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/unarchive', name: 'app.games.unarchive')]
    public function unarchive(
        Game $game,
        Request $request
    ): Response {
        $form = $this->createForm(UnArchiveGameType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $game->unArchive();
            $this->gameService->saveGame($game);
            $this->addFlash('success', 'Game unarchived successfully.');
            return $this->redirectToRoute('app.games.index');

        }
        $pageVars = [
            'pageTitle' => sprintf("Archive %s", $game->getName()),
            'form' => $form
        ];
        return $this->render('/visitor/games/archive.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/delete', name: 'app.games.delete')]
    public function delete(
        Game $game,
        Request $request
    ): Response {
        $title = sprintf('Really delete %s?', $game->getName());
        $form = $this->createForm(DeleteGameType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->gameService->deleteGame($game);

            $this->addFlash('success', sprintf("Game %s delete successfully", $game->getName()));
            return $this->redirectToRoute('app.games.index');
        }
        $pageVars = [
            'pageTitle' => $title,
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => $title,
                    'active' => true
                ],
            ],
            'form' => $form
        ];
        return $this->render('/visitor/games/delete.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GamePlayerVoter::PLAY_GAME, 'game')]
    #[Route('games/{gameSlug}/play', name: 'app.games.play')]
    public function play(
        Game $game,
        Request $request
    ) {
        $pageVars = [
            'pageTitle' => sprintf("Play %s", $game->getName()),
            'heroImageUrl' => $game->getHeroImageFile()?->getPath(),
            'game' => $game
        ];
        return $this->render('/visitor/games/play.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
