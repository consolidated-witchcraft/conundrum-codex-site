<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles;

use App\Controller\AbstractBaseController;
use App\Form\Mapper\DynamicInstanceDataMapper;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\PuzzleInstance\Service\Interfaces\PuzzleInstanceServiceInterface;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractPuzzleController extends AbstractBaseController
{

    public const string ADD_TO_GAME_SESSION_KEY = 'add-to-game';

    public function __construct(
        protected readonly PuzzleTemplateServiceInterface $puzzleTemplateService,
        protected readonly PuzzleInstanceServiceInterface $puzzleInstanceTemplateService,
        protected readonly SerializerInterface            $serializer,
        protected readonly EntityManagerInterface         $entityManager,
        protected readonly SluggerInterface               $slugger,
        protected readonly ValidatorInterface             $validator,
        protected readonly FilesServiceInterface          $filesService,
        protected readonly DynamicInstanceDataMapper      $dynamicInstanceDataMapper,
    ) {
    }
}
