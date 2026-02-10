<?php

declare(strict_types=1);

namespace App\Controller\EventManager;

use App\Controller\AbstractBaseController;
use App\Services\Events\Service\EventServiceInterface;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEventManagerController extends AbstractBaseController
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
        protected readonly EventServiceInterface $eventService,
        protected readonly UserServiceInterface $userService,
        protected readonly FilesServiceInterface $filesService,
        protected readonly SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/images/events/hero')] protected readonly string $eventsHeroImagesDirectory,
    ) {}
}
