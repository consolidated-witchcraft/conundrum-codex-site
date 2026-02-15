<?php

namespace App\Controller\EventManager;

use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\EventManager\EventDto;
use App\Dto\Visitor\User\PostalAddressFieldDto;
use App\Form\EventManager\EventType;
use App\Form\EventManager\TicketTypeType;
use App\Security\EventManagerVoter;
use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\Ticket\Domain\Exceptions\InvalidTicketTypeDefinitionException;
use App\Services\Ticket\Dto\TicketTypeDto;
use App\Services\Ticket\Dto\TicketTypesDto;
use App\Services\User\Domain\User;
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
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $heroImage = $this->handleFileUpload($form->get('heroImage')->getData(), $this->eventsHeroImagesDirectory);

            $location = PostalAddress::makeFromPostalAddressFieldDto($user, $dto->location);
            $location = $this->postalAddressService->save($location);
            $event = new Event(
                title: $dto->title,
                eventSlug: $dto->eventSlug,
                description: $dto->description,
                heroImage: $heroImage,
                location: $location,
                prebookOnly: $dto->prebookOnly,
                organisation: $organisation,
                startDate: $dto->startDate,
                endDate: $dto->endDate,
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
                return $this->redirectToRoute('events.organisations.ticket-types', ['organisationHandle' => $organisation->getHandle(), 'eventSlug' => $event->getEventSlug()]);
            }
        }
        $pageVars = [
            'pageTitle' => sprintf("Create event for %s", $organisation->getName()),
            'form' => $form
        ];
        return $this->render('eventManager/events/create.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(EventManagerVoter::MANAGE_EVENTS_ACTION, 'organisation')]
    #[Route('manage-events/organisations/{organisationHandle}/events/{eventSlug}/edit', name: 'events.organisations.events.edit')]
    public function editEvent(
        Organisation $organisation,
        Event $event,
        Request $request,
    ): Response {
        $dto = EventDto::makeFromEvent($event);
        $form = $this->createForm(EventType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $heroImage = $this->handleFileUpload($form->get('heroImage')->getData(), $this->eventsHeroImagesDirectory);

            $event->setHeroImage($heroImage);
            $event->setTitle($dto->title);
            $event->setDescription($dto->description);
            $event->setStartDate($dto->startDate);
            $event->setEndDate($dto->endDate);
            if ($dto->location instanceof PostalAddressFieldDto) {
                $postalAddress = PostalAddress::makeFromPostalAddressFieldDto($organisation->getOwner(), $dto->location);
                $this->postalAddressService->save($postalAddress);
                $event->setLocation($postalAddress);
            }

            $this->eventService->saveEvent($event);

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
                $this->addFlash('success', 'Event updated.');
                return $this->redirectToRoute('events.organisations.manage', ['organisationHandle' => $organisation->getHandle()]);
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
        $dto = TicketTypesDto::makeFromEvent($event);
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->createAccessDeniedException();
        }
        $form = $this->createForm(TicketTypeType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $success = true;
            /**
             * @var TicketTypeDto[] $ticketTypes
             */

            $errors = [];
            foreach ($dto->ticketTypes as $ticketTypeData) {
                $success = true;

                if ($ticketTypeData->id !== null && $ticketTypeData->id !== 0) {
                    $ticketType = $this->eventService->findTicketType($ticketTypeData->id);
                    $ticketType->setName($ticketTypeData->name);
                    $ticketType->setDescription($ticketTypeData->description);
                    $ticketType->setPriceCentessimal($ticketTypeData->priceCentessimal);
                    $violations = $this->validator->validate($ticketType);

                    $product = $ticketType->getProduct();
                    $product->setPreTaxPriceCentessimal($ticketTypeData->priceCentessimal);
                    $product->getProductStock()->setInitialStockLevel($ticketTypeData->initialQuantity);
                    $product->getProductStock()->setCurrentStockLevel($ticketTypeData->initialQuantity);

                    if (count($violations) > 0) {
                        $success = false;
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getMessage();
                        }
                    }
                    if ($success) {
                        $this->eventService->saveTicketType($ticketType);
                    }
                } else {

                    if (!$ticketTypeData->isEmpty()) {
                        try {
                            $ticketType = $this->ticketService->generateTicketTypeFromDto($ticketTypeData, $event, $user);
                        } catch (InvalidTicketTypeDefinitionException $e) {
                            $success = false;
                            foreach ($e->getErrors() as $error) {
                                $this->addFlash('error', $error->getMessage());
                            }
                        }
                    }
                }
            }
            if (!$success) {
                $this->addFlash('error', implode("\n", $errors));
            } else {
                $this->addFlash('success', 'Ticket types updated.');
                return $this->redirectToRoute('events.organisations.manage', ['organisationHandle' => $organisation->getHandle()]);
            }



        }
        $pageVars = [
            'pageTitle' => sprintf("Manage Ticket Types for %s", $event->getTitle()),
            'form' => $form,
            'event' => $event
        ];
        return $this->render('eventManager/events/ticketTypes/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
