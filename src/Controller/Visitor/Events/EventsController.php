<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Events;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Events\PersonalDetailsDto;
use App\Form\EventManager\PersonalDetailsType;
use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Events\Service\EventServiceInterface;
use App\Services\User\Domain\User;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EventsController extends AbstractBaseController
{

    private const string TICKET_DATA_KEY = 'ticketData';

    private const string PERSONAL_DATA_KEY = 'personalData';

    public function __construct(
        private readonly SerializerInterface $serializer,
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
                    'required' => false,
                    'attr' => [
                        'min' => 0,
                        'max' => 10,
                        'class' => 'number-spinner',
                        'is' => 'number-spinner',
                        'step' => 1,
                        'length' => 2,
                        'placeholder' => 1
                    ],
                    'html5' => true,
                    'help' => $ticketType->getDescription(),

                ]
            );
        }
        $builder->add('submit', SubmitType::class, [
            'label' => 'Proceed to your details',
        ]);
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $totalTicketCount = 0;
            foreach($form->getData() as $ticketTypeSlug => $amount) {
                $totalTicketCount += $amount;
            }
            if ($totalTicketCount <= 0) {
                $this->addFlash('error', 'You must add at least one ticket to proceed!');
            } else {
                $session = $request->getSession();
                $serializedData = $this->serializer->serialize(
                    $form->getData(),
                    'json'
                );
                $session->set(self::TICKET_DATA_KEY, $form->getData());
                return $this->redirectToRoute('app.events.tickets.personal-details', [
                    'eventSlug' => $event->getEventSlug(),
                    'organisationHandle' => $organisation->getHandle(),
                ]);
            }
        }
        $pageVars = [
            'pageTitle' => sprintf("Get tickets for %s", $event->getTitle()),
            'form' => $form
        ];
        return $this->render('visitor/events/tickets/select-tickets.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/events/{organisationHandle}/{eventSlug}/tickets/personal-details', name: 'app.events.tickets.personal-details')]
    public function personalDetails(
        Event $event,
        Organisation $organisation,
        Request $request
    ) {
        $session = $request->getSession();
        $submittedTicketDetails = $session->get(self::TICKET_DATA_KEY);
        $ticketDetails = [];
        $totalCostCentessimal = 0;
        foreach ($submittedTicketDetails as $ticketTypeSlug => $amount) {
            $type = $this->eventService->getTicketTypeByEventAndSlug($event, $ticketTypeSlug);
            $ticketDetails[] = [
                'type' => $type,
                'cost' => $type->getPriceDecimal(),
                'amount' => $amount ?? 0
            ];
            $totalCostCentessimal += ($amount * $type->getPriceCentessimal());
        }
        $user = $this->getUser();
        $dto = $user instanceof User ? PersonalDetailsDto::makeFromUser($user) : new PersonalDetailsDto();
        $form = $this->createForm(PersonalDetailsType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session->set(self::PERSONAL_DATA_KEY, $dto);
        }
        $pageVars = [
            'pageTitle' => "Enter your personal details",
            'form' => $form,
            'ticketDetails' => $ticketDetails,
            'totalPriceDecimal' => $totalCostCentessimal/100,
        ];
        return $this->render('visitor/events/tickets/personal-details.html.twig', $this->populatePageVars($pageVars, $request));

    }
}
