<?php

namespace App\Command;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\User\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:files:migrate-avatars.',
    description: 'Migrate User Avatars to managed files.',
)]
class MigrateUserAvatarsCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
        private readonly FilesServiceInterface $filesService
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

        $users = $this->userService->findAll();
        foreach ($users as $user) {
            $avatarUrl = $user->getAvatarUrl();
            $file = fopen(__DIR__ . '/../../public' . $avatarUrl, 'r');
            if ($file) {

                $io->success(sprintf('Migrating user avatar: %s', $avatarUrl));
                $file = new ManagedFile(
                    path: $avatarUrl,
                    mimeType: mime_content_type($file),
                    title: sprintf("%s' avatar", $user->getUserName()),
                    description: sprintf("%s' avatar", $user->getUserName()),
                    uploader: $user
                );
                $this->filesService->save($file);
                $user->setAvatar($file);
                $this->userService->saveUser($user);
            }
        }

        return Command::SUCCESS;
    }
}
