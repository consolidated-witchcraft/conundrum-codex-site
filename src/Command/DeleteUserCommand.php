<?php

namespace App\Command;

use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[AsCommand(
    name: 'app:user:delete',
    description: 'Delete a user.',
)]
class DeleteUserCommand extends Command
{
    public function __construct(
        private UserService                $userService,
        private EntityManagerInterface     $entityManager,
        private GameEntityServiceInterface $gameService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The E-Mail address of the user to be removed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $this->userService->loadUserByIdentifier($email);
                $user = $this->userService->loadUserByIdentifier($email);
                if ($user instanceof User) {
                    $userInvitations = $this->gameService->findInvitationsByEmailAddress($email);
                    foreach ($userInvitations as $userInvitation) {
                        $this->entityManager->remove($userInvitation);
                    }
                    $this->entityManager->remove($user);
                    $this->entityManager->flush();
                    $io->success('User Deleted.');

                    return Command::SUCCESS;
                }

                $io->error('Unknown error.');
                return Command::FAILURE;

            } catch (UserNotFoundException $e) {
                $io->error(sprintf($e->getMessage(), $email));
                return Command::FAILURE;
            } catch (CustomUserMessageAccountStatusException $e) {
                $io->error($e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $io->error('Invalid email address.');
            return Command::FAILURE;
        }
    }
}
