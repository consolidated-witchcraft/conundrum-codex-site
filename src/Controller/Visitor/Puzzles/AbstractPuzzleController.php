<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles;

use App\Controller\AbstractBaseController;
use App\Form\Mapper\DynamicInstanceDataMapper;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use App\Services\PuzzleInstance\Service\Interfaces\PuzzleInstanceEntityServiceInterface;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
abstract class AbstractPuzzleController extends AbstractBaseController
{
    public const string ADD_TO_GAME_SESSION_KEY = 'add-to-game';
    public function __construct(
        protected readonly PuzzleTemplateServiceInterface       $puzzleTemplateService,
        protected readonly PuzzleInstanceEntityServiceInterface $puzzleInstanceTemplateService,
        protected readonly SerializerInterface                  $serializer,
        protected readonly EntityManagerInterface               $entityManager,
        protected readonly SluggerInterface                     $slugger,
        protected readonly ValidatorInterface                   $validator,
        protected readonly ManagedFileServiceInterface          $filesService,
        protected readonly ManagedImageServiceInterface         $managedImageService,
        protected readonly DynamicInstanceDataMapper            $dynamicInstanceDataMapper,
        FeatureSwitcherServiceInterface                         $featureSwitcherService
    ) {
        parent::__construct($featureSwitcherService);
    }
}
