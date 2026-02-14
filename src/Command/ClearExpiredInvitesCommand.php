<?php

namespace App\Command;

use App\Services\Game\Infrastructure\GameInvitationRepository;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clear-expired-invites',
    description: 'Deletes expired game invitations from the database',
)]
class ClearExpiredInvitesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GameEntityServiceInterface $gameService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $expiredInvitations = $this->gameService->getExpiredInvitations();

        $count = 0;
        foreach ($expiredInvitations as $expiredInvitation) {
            $io->info("Invitation with code {$expiredInvitation->getInvitationCode()} is expired");
            $count++;
        }
        if ($count === 0) {
            $io->info('There are no expired invitations to clear');
        }

        foreach ($expiredInvitations as $expiredInvitation) {

            $io->info(sprintf('Removing invitation with code %s', $expiredInvitation->getInvitationCode()));
            $this->entityManager->remove($expiredInvitation);
        }
        $this->entityManager->flush();



        $io->success(sprintf('%s expired Invitations cleared successfully.', $count));

        return Command::SUCCESS;
    }
}
