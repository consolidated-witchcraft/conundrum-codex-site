<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Events;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Events\TicketPurchaseDto;
use App\Form\Visitor\Events\TicketPurchaseType;
use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Events\Service\EventServiceInterface;
use App\Services\Product\Service\Interfaces\ProductServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventsController extends AbstractBaseController
{

    private const string TICKET_DATA_KEY = 'ticketData';

    private const string PERSONAL_DATA_KEY = 'personalData';

    public function __construct(
        private readonly EventServiceInterface   $eventService,
        private readonly ProductServiceInterface $productService,
        private readonly UserServiceInterface    $userService,

    ) {}

    #[Route('/events', name: 'app.events.index')]
    public function index(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Upcoming Events',
            'events' => $this->eventService->getUpcomingEvents()
        ];
        return $this->render('visitor/events/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/events/{organisationHandle}/{eventSlug}/get-tickets', name: 'app.events.tickets')]
    public function getTickets(
        Event $event,
        Organisation $organisation,
        Request $request
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('info', 'Please log in or register to continue.');
            return $this->redirectToRoute('app.auth.register', [
                'redirectRoute' => 'google.com'
            ]);
        }
        $dto = TicketPurchaseDto::makeFromEvent($event);
        $form = $this->createForm(TicketPurchaseType::class, $dto);
        $pageVars = [
            'pageTitle' => sprintf("Get tickets for %s", $event->getTitle()),
            'form' => $form
        ];
        return $this->render('visitor/events/tickets/select-tickets.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
