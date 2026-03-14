<?php

declare(strict_types=1);

namespace App\Controller\EventManager;

use App\Services\User\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventManagerDashboardController extends AbstractEventManagerController
{
    #[Route('manage-events/dashboard', name: 'events.dashboard')]
    #[IsGranted('create_events')]
    #[IsGranted('FS_EVENTS_SYSTEM')]
    public function dashboard(Request $request): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }
        $organisations = $this->eventService->getOrganisationsByManager($user);

        $pagerVars = [
            'pageTitle' => 'Event Manager Dashboard',
            'organisations' => $organisations,
        ];
        return $this->render('eventManager/dashboard.html.twig', $this->populatePageVars($pagerVars, $request));
    }
}
