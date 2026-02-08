<?php

declare(strict_types=1);

namespace App\Controller\EventManager;

use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventManagerController extends AbstractBaseController
{
    #[Route('manage-events/dashboard', name: 'events.dashboard')]
    #[IsGranted('create_events')]
    public function dashboard(Request $request): Response {

    }
}
