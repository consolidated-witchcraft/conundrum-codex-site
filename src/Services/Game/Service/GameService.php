<?php

declare(strict_types=1);

namespace App\Services\Game\Service;

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\Game\Infrastructure\GameInvitationRepository;
use App\Services\Game\Infrastructure\GameRepository;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use App\Services\Puzzle\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\Puzzle\Infrastructure\CodeGenerator;
use App\Services\Puzzle\Service\Interfaces\PuzzleInstanceServiceInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class GameService implements GameServiceInterface
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameInvitationRepository $gameInvitationRepository,
        private PuzzleInstanceServiceInterface $puzzleInstanceService,
        private EntityManagerInterface $entityManager,
        private PuzzleTemplateRegistryInterface $puzzleTemplateRegistry,
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
            $game->setStaticPuzzleInstances($this->puzzleInstanceService->getStaticPuzzleInstancesForGame($game));
        }
        foreach ($game->getDynamicPuzzleInstances() as $dynamicPuzzleInstance) {

            $template = $this->puzzleTemplateRegistry->getTemplate($dynamicPuzzleInstance->getTemplateSlug());
            if (!$template) {
                throw new MismappedPuzzleTemplateException();
            }
            $dynamicPuzzleInstance->setTemplate($template);
        }



        return $game;
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
}
