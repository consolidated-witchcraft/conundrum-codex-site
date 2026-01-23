<?php

declare(strict_types=1);

namespace App\Services\Game\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainServiceInterface;
use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\User;

interface GameServiceInterface extends DomainServiceInterface
{
    public function getRandomUnusedSlug(): string;

    public function findOneBySlug(string $slug): ? Game;

    /**
     * @param Game $game
     * @return GameInvitation[]
     */
    public function getOutstandingInvitationsForGame(Game $game): iterable;

    public function findInvitationByCode(string $invitationCode): ? GameInvitation;

    public function findInvitationByCodeAndEmailAddress(string $invitationCode, string $emailAddress): ? GameInvitation;

    /**
     * @param string $emailAddress
     * @return GameInvitation[]
 */
    public function findInvitationsByEmailAddress(string $emailAddress): iterable;

    /**
     * @return GameInvitation[]
     */
    public function getExpiredInvitations(): iterable;

    /**
     * @param User $user
     * @return GameInvitation[]
     */
    public function getOutstandingInvitationsForUser(User $user): iterable;

    public function saveGame(Game $game): Game;

    public function deleteGame(Game $game): void;

    public function findAllUnarchivedGames(): iterable;

    public function find(int $id): ? Game;
}
