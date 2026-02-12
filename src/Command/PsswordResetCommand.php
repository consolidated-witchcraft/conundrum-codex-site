<?php

namespace App\Command;

use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Service\UserService;
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
    name: 'app:user:password',
    description: 'Reset a user\'s password',
)]
class PsswordResetCommand extends Command
{
    public function __construct(private readonly UserService $userService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The E-Mail address of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $user = $this->userService->loadUserByIdentifier($email);
            } catch (UserNotFoundException $e) {
                $io->error(sprintf('No user with email "%s" found.', $email));
                return Command::FAILURE;
            } catch (CustomUserMessageAccountStatusException $e) {
                $io->error($e->getMessage());
                return Command::FAILURE;
            }


            if ($user instanceof User) {
                $hashedPassword = $this->userService->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
                $this->userService->saveUser($user);
                $io->success(sprintf('User "%s" was successfully reset.', $email));
                return Command::SUCCESS;
            }
            $io->success('User Promoted.');

            return Command::SUCCESS;
        } else {
            $io->error('Invalid email address.');
            return Command::FAILURE;
        }
    }
}
