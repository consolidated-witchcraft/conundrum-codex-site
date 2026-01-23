<?php

namespace App\Command;

use App\Services\User\Service\Interfaces\UserServiceInterface;
use App\Services\User\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:populate_account_types',
    description: 'Populates user account Types',
)]
class PopulateMissingUserAccountTypesCommand extends Command
{
    public function __construct(
        private readonly UserServiceInterface $userService,
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
        $users = $this->userService->findUsersWithoutAccountType();
        $defaultAccountType = $this->userService->getAccountTypeByHandle("free");
        if (empty($users)) {
            $io->warning("No users with an unmapped account type ahve been found.");
            return Command::SUCCESS;
        }
        foreach ($users as $user) {
            $io->info(sprintf("User %s is missing account type. Populating", $user->getEmail()));
            $user->setUserAccountType($defaultAccountType);
            $this->userService->saveUser($user);
        }

        return Command::SUCCESS;
    }
}
