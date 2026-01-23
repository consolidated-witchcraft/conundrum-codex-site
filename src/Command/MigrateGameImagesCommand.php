<?php

namespace App\Command;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookRepository;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectRepository;
use App\Services\User\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Hoa\File\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:files:migrate-games.',
    description: 'Migrate User Casebook Images to managed files.',
)]
class MigrateGameImagesCommand extends Command
{
    public function __construct(

        private readonly GameServiceInterface $gameService,
        private readonly FilesServiceInterface $filesService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $games = $this->gameService->findAllGames(true);
        foreach ($games as $game) {
            $author = $game->getGamesMaster();
            $heroImageUrl = $game->getHeroImageUrl();
            if ($heroImageUrl) {
                $file = fopen(__DIR__ . '/../../public' . $heroImageUrl, 'r');
                if ($file) {

                    $io->success(sprintf('Migrating Game Image: %s', $heroImageUrl));
                    $file = new ManagedFile(
                        path: $heroImageUrl,
                        mimeType: mime_content_type($file),
                        title: $game->getName(),
                        description: $game->getDescription(),
                        uploader: $author
                    );
                    $this->filesService->save($file);
                    $game->setHeroImageFile($file);
                    $this->gameService->saveGame($game);
                }
            }
        }


        return Command::SUCCESS;
    }
}
