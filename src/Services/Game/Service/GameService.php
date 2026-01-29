<?php

declare(strict_types=1);

namespace App\Services\Game\Service;

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\Game\Domain\GameSession;
use App\Services\Game\Infrastructure\GameInvitationRepository;
use App\Services\Game\Infrastructure\GameRepository;
use App\Services\Game\Infrastructure\GameSessionRepository;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use App\Services\Puzzle\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\Puzzle\Infrastructure\CodeGenerator;
use App\Services\Puzzle\Service\Interfaces\PuzzleInstanceServiceInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

readonly class GameService implements GameServiceInterface
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameInvitationRepository $gameInvitationRepository,
        private PuzzleInstanceServiceInterface  $puzzleInstanceService,
        private EntityManagerInterface $entityManager,
        private PuzzleTemplateRegistryInterface $puzzleTemplateRegistry,
        private GameSessionRepository $gameSessionRepository
    ) {}

    /**
     * @throws RandomException
     */
    public function getRandomUnusedSlug(): string {

        $randomSlug = CodeGenerator::generateRandomCode();
        if (null !== $this->gameRepository->findOneBySlug($randomSlug)) {
            return $this->getRandomUnusedSlug();
        }
        return $randomSlug;
    }

    /**
     * @throws MismappedPuzzleTemplateException
     */
    public function findOneBySlug(string $slug): ? Game {
        $game = $this->gameRepository->findOneBySlug($slug);
        if ($game) {
           $this->mapPuzzleInstances($game);
        }

        return $game;
    }

    /**
     * @throws MismappedPuzzleTemplateException
     */
    private function mapPuzzleInstances(Game $game): void
    {
        $game->setStaticPuzzleInstances($this->puzzleInstanceService->getStaticPuzzleInstancesForGame($game));
        foreach ($game->getDynamicPuzzleInstances() as $dynamicPuzzleInstance) {
            $template = $this->puzzleTemplateRegistry->getTemplate($dynamicPuzzleInstance->getTemplateSlug());
            if (!$template) {
                throw new MismappedPuzzleTemplateException();
            }
            $dynamicPuzzleInstance->setTemplate($template);
        }
    }
    /**
     * @param Game $game
     * @return GameInvitation[]
     */
    public function getOutstandingInvitationsForGame(Game $game): iterable
    {
        return $this->gameInvitationRepository->getOutstandingInvitationsForGame($game);
    }

    public function findInvitationByCode(string $invitationCode): ? GameInvitation {
        return $this->gameInvitationRepository->findByInvitationCode($invitationCode);
    }

    public function findInvitationByCodeAndEmailAddress(string $invitationCode, string $emailAddress): ? GameInvitation {
        return $this->gameInvitationRepository->findByInvitationCodeAndEmailAddress($invitationCode, $emailAddress);
    }

    /**
     * @return GameInvitation[]
     */
    public function getExpiredInvitations(): iterable
    {
        return $this->gameInvitationRepository->getExpiredInvitations();
    }

    /**
     * @param User $user
     * @return GameInvitation[]
     */
    public function getOutstandingInvitationsForUser(User $user): iterable
    {
        return $this->gameInvitationRepository->getOutstandingInvitationsForUser($user);
    }


    /**
     * @return GameInvitation[]
     */
    public function findInvitationsByEmailAddress(string $emailAddress): iterable
    {
        return $this->gameInvitationRepository->findInvitationsByEmailAddress($emailAddress);
    }

    public function saveGame(Game $game): Game
    {
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return $game;
    }

    public function deleteGame(Game $game): void
    {
        $this->entityManager->remove($game);
        $this->entityManager->flush();
    }

    /**
     * @throws MismappedPuzzleTemplateException
     */
    public function find(int $id): ?Game
    {
        $game = $this->gameRepository->find($id);
        if ($game) {
            $this->mapPuzzleInstances($game);
        }
        return $game;
    }

    /**
     * @param bool $includeArchived
     * @return Game[]
     * @throws MismappedPuzzleTemplateException
     */
    public function findAllGames(bool $includeArchived = false): iterable
    {
        $games = $this->gameRepository->findAllGames($includeArchived);
        foreach ($games as $game) {
            $this->mapPuzzleInstances($game);
        }
        return $games;
    }

    public function saveInvitation(GameInvitation $invitation): GameInvitation
    {
        $this->entityManager->persist($invitation);
        $this->entityManager->flush();
        return $invitation;
    }

    public function saveGameSession(GameSession $gameSession): GameSession
    {
        $this->entityManager->persist($gameSession);
        $this->entityManager->flush();
        return $gameSession;
    }

    public function findGameSessionById(int $id): ?GameSession
    {
        return $this->gameSessionRepository->find($id);
    }
}
