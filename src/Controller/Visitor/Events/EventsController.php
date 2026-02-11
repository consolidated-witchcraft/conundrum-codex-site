<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Events;

use App\Controller\AbstractBaseController;
use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Events\Service\EventServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventsController extends AbstractBaseController
{
    public function __construct(
        private readonly EventServiceInterface $eventService
    ) {}

    #[Route('/events', name: 'app.events.index')]
    public function index(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Upcoming Events',
            'events' => $this->eventService->getUpcomingEvents()
        ];
        return $this->render('visitor/events/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/events/{organisationHandle}/{eventSlug}/tickets', name: 'app.events.tickets')]
    public function getTickets(
        Event $event,
        Organisation $organisation,
        Request $request
    ): Response {
        $builder = $this->createFormBuilder();
        foreach ($event->getTicketTypes() as $ticketType) {
            $builder->add(
                $ticketType->getSlug(),
                NumberType::class,
                [
                    'label' => sprintf(" %s - £%s", $ticketType->getName(), $ticketType->getPriceDecimal()),
                    'attr' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                    'data' => 0,
                    'html5' => true,
                    'help' => $ticketType->getDescription(),

                ]
            );
        }
        $builder->add('submit', SubmitType::class, [
            'label' => 'Proceed to your details',
        ]);
        $form = $builder->getForm();
        $pageVars = [
            'pageTitle' => sprintf("Get tickets for %s", $event->getTitle()),
            'form' => $form
        ];
        return $this->render('visitor/events/tickets.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
