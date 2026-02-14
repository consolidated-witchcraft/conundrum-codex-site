<?php

declare(strict_types=1);

namespace App\Security;

use App\ApiResource\PuzzleInstance\ApiPuzzleInstanceDtoInterface;
use App\ApiResource\PuzzleInstance\Dynamic\ApiDynamicPuzzleInstanceDto;
use App\Services\Game\Domain\Game;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, ApiDynamicPuzzleInstanceDto>
 */
class PuzzleInstancePlayerVoter extends Voter
{
    public const string PLAY_INSTANCE = 'play_instance';

    public function __construct(private readonly GameEntityServiceInterface $gameService) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::PLAY_INSTANCE) {
            return false;
        }

        if (!$subject instanceof ApiPuzzleInstanceDtoInterface) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($subject instanceof ApiPuzzleInstanceDtoInterface && $user instanceof User) {
            $game = $this->gameService->findOneBySlug($subject->gameSlug);
            if (!$game instanceof Game) {
                return false;
            }

            if ($game->getGamesMaster()->getUserIdentifier() === $user->getUserIdentifier()) {
                return true;
            }

            foreach ($game->getPlayers() as $player) {
                if ($player->getUserIdentifier() === $user->getUserIdentifier()) {
                    return true;
                }
            }
        }

        return false;
    }
}
