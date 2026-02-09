<?php

declare(strict_types=1);

namespace App\Controller\EventManager;

use App\Controller\AbstractBaseController;
use App\Services\Events\Service\EventServiceInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEventManagerController extends AbstractBaseController
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
        protected readonly EventServiceInterface $eventService
    ) {}
}
