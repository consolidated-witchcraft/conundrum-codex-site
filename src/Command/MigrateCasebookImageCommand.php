<?php

namespace App\Command;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookRepository;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectRepository;
use App\Services\User\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:files:migrate-casebook.',
    description: 'Migrate User Casebook Images to managed files.',
)]
class MigrateCasebookImageCommand extends Command
{
    public function __construct(

        private readonly CasebookSubjectRepository $casebookSubjectRepository,
        private readonly FilesServiceInterface $filesService,
        private EntityManagerInterface $entityManager,
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

        $subjects = $this->casebookSubjectRepository->findAll();
        foreach ($subjects as $subject) {
            $author = $subject->getCasebook()->getGame()->getGamesMaster();
            $subjectImageUrl = $subject->getCasebookSubjectImage();
            if ($subjectImageUrl) {
                $file = fopen(__DIR__ . '/../../public' . $subjectImageUrl, 'r');
                if ($file) {

                    $io->success(sprintf('Migrating casebookImage: %s', $subjectImageUrl));
                    $file = new ManagedFile(
                        path: $subjectImageUrl,
                        mimeType: mime_content_type($file),
                        title: $subject->getName(),
                        description: $subject->getDescription(),
                        uploader: $author
                    );
                    $this->filesService->save($file);
                    $subject->setCasebookSubjectImageFile($file);
                    $this->entityManager->persist($subject);
                }
            }
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
