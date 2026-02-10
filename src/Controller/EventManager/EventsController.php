<?php

namespace App\Controller\EventManager;

use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\EventManager\EventDto;
use App\Dto\EventManager\TicketTypeDto;
use App\Form\EventManager\EventType;
use App\Form\EventManager\TicketTypeType;
use App\Security\EventManagerVoter;
use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Events\Domain\TicketType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventsController extends AbstractEventManagerController
{
    use HandlesFileUploadsTrait;
    #[IsGranted(EventManagerVoter::MANAGE_EVENTS_ACTION, 'organisation')]
    #[Route('manage-events/organisations/{organisationHandle}/events', name: 'events.organisations.manage')]
    public function manageEvents(
        Organisation $organisation,
        Request $request,
    ): Response {
        $pageVars = [
            'pageTitle' => sprintf("Events for %s", $organisation->getName()),
            'organisation' => $organisation,
        ];
        return $this->render('eventManager/events/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(EventManagerVoter::MANAGE_EVENTS_ACTION, 'organisation')]
    #[Route('manage-events/organisations/{organisationHandle}/events/create', name: 'events.organisations.create')]
    public function createEvent(
        Organisation $organisation,
        Request $request,
    ): Response {
        $dto = new EventDto($organisation);
        $form = $this->createForm(EventType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $heroImage = $this->handleFileUpload($form->get('heroImage')->getData(), $this->eventsHeroImagesDirectory);

            $event = new Event(
                title: $dto->title,
                description: $dto->description,
                organisation: $organisation,
                startDate: $dto->startDate,
                endDate: $dto->endDate,
                eventSlug: $dto->eventSlug,
                heroImage: $heroImage,
                prebookOnly: $dto->prebookOnly
            );

            $success = true;
            $violations = $this->validator->validate($event);
            if ($violations->count() > 0) {
                foreach ($violations as $violation) {
                    $success = false;
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->eventService->saveEvent($event);
                $this->addFlash('success', 'Event created.');
            }
        }
        $pageVars = [
            'pageTitle' => sprintf("Create event for %s", $organisation->getName()),
            'form' => $form
        ];
        return $this->render('eventManager/events/create.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(EventManagerVoter::MANAGE_EVENTS_ACTION, 'organisation')]
    #[Route('manage-events/organisations/{organisationHandle}/events/{eventSlug}/ticket-types', name: 'events.organisations.ticket-types')]
    public function ticketTypes(
        Organisation $organisation,
        Event $event,
        Request $request,
    ) {
        $form = $this->createForm(TicketTypeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var TicketTypeDto[] $ticketTypes
             */
            $ticketTypes = $form->get('ticketType')->getData();

            $errors = [];
            foreach ($ticketTypes as $ticketTypeData) {
                $success = true;
                $ticketType = new TicketType(
                    name: $ticketTypeData->name,
                    description: $ticketTypeData->description,
                    priceCentessimal: $ticketTypeData->priceCentessimal,
                    event: $event,
                    slug: $ticketTypeData->slug,
                );
                $violations = $this->validator->validate($ticketType);
                if (count($violations) > 0) {
                    foreach ($violations as $violation) {
                        $errors[] = $violation->getMessage();
                        $success = false;
                    }
                }
                if ($success) {
                    $this->eventService->saveTicketType($ticketType);
                }
            }
            if (count($errors) > 0) {
                $this->addFlash('error', implode("\n", $errors));
            } else {
                $this->addFlash('success', 'Ticket types created.');
                return $this->redirectToRoute('events.organisations.manage', ['organisationHandle' => $organisation->getHandle()]);
            }



        }
        $pageVars = [
            'pageTitle' => sprintf("Create event for %s", $organisation->getName()),
            'form' => $form,
            'event' => $event
        ];
        return $this->render('eventManager/events/ticketTypes/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
