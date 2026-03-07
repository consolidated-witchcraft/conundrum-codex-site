<?php

declare(strict_types=1);

namespace App\Controller\EventManager;

use App\Controller\AbstractBaseController;
use App\Services\Events\Service\EventServiceInterface;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\Files\Service\Interfaces\FilesEntityServiceInterface;
use App\Services\PostalAddress\Service\PostalAddressServiceInterface;
use App\Services\Product\Service\Interfaces\ProductServiceInterface;
use App\Services\Ticket\Service\Interfaces\TicketServiceInterface;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEventManagerController extends AbstractBaseController
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
        protected readonly EventServiceInterface $eventService,
        protected readonly UserServiceInterface $userService,
        protected readonly FilesEntityServiceInterface $filesService,
        protected readonly SluggerInterface $slugger,
        protected readonly PostalAddressServiceInterface $postalAddressService,
        protected readonly ProductServiceInterface $productService,
        protected readonly TicketServiceInterface $ticketService,
        FeatureSwitcherServiceInterface $featureSwitcherService,
        #[Autowire('%kernel.project_dir%/public/uploads/images/events/hero')] protected readonly string $eventsHeroImagesDirectory,
        #[Autowire('%kernel.project_dir%/public/uploads/images/organisations/logos')] protected readonly string $organisationLogoImagesDirectory,
    ) {
        parent::__construct($featureSwitcherService);
    }
}
