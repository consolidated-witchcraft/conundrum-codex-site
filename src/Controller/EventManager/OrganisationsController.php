<?php

declare(strict_types=1);

namespace App\Controller\EventManager;

use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\EventManager\OrganisationDto;
use App\Form\EventManager\OrganisationDeactivationConfirmationType;
use App\Form\EventManager\OrganisationType;
use App\Services\Events\Domain\Organisation;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrganisationsController extends AbstractEventManagerController
{
    use HandlesFileUploadsTrait;


    #[Route('manage-events/organisations/add', name: 'events.organisations.add')]
    #[IsGranted('create_event_organisations')]
    public function create(Request $request): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }
        $dto = new OrganisationDto(
            owner: $user,
        );
        $form = $this->createForm(OrganisationType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $organisationLogo = $form->get('organisationLogo')->getData();
            $this->handleFileUpload($organisationLogo, $this->organisationLogoImagesDirectory);
            $organisation = new Organisation(
                name: $dto->name,
                handle: $dto->handle,
                organisationLogo: $organisationLogo,
                owner: $user,
                events: new ArrayCollection(),
                managers: new ArrayCollection($dto->managers),
            );
            $success = true;
            $violations = $this->validator->validate($organisation);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->eventService->saveOrganisation($organisation);
                $this->addFlash('success', 'Organisation created.');
                return $this->redirectToRoute('events.dashboard');
            }
        }
        $pageVars = [
            'pageTitle' => 'Create a new Organisation',
            'form' => $form,
        ];
        return $this->render('eventManager/organisations/add.html.twig', $this->populatePageVars(
            pageVars: $pageVars,
            request: $request,
        ));
    }

    #[Route('manage-events/organisations/{organisationHandle}/edit', name: 'events.organisations.edit')]
    #[IsGranted('create_event_organisations')]
    public function edit(
        Organisation $organisation,
        Request $request
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }
        $dto = OrganisationDto::makeFromOrganisation($organisation);
        $form = $this->createForm(OrganisationType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $organisationLogo = $this->handleFileUpload($form->get('organisationLogo')->getData(), $this->organisationLogoImagesDirectory);
            $organisation->setOrganisationLogo($organisationLogo);
            $organisation->setName($dto->name);
            $organisation->setHandle($dto->handle);

            $managers = $dto->managers instanceof Collection ? $dto->managers : new ArrayCollection($dto->managers);
            foreach ($managers as $manager) {
                $manager->setOrganisation($organisation);
                $this->userService->saveUser($manager);
            }
            $organisation->setManagers($managers);
            $success = true;
            $violations = $this->validator->validate($organisation);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->eventService->saveOrganisation($organisation);
                $this->addFlash('success', 'Organisation updated.');
                return $this->redirectToRoute('events.dashboard');
            }
        }
        $pageVars = [
            'pageTitle' => sprintf("Edit organisation '%s'", $organisation->getName()),
            'form' => $form,
        ];
        return $this->render('eventManager/organisations/edit.html.twig', $this->populatePageVars(
            pageVars: $pageVars,
            request: $request,
        ));
    }

    #[Route('manage-events/organisations/{organisationHandle}/deactivate', name: 'events.organisations.deactivate')]
    #[IsGranted('create_event_organisations')]
    public function deactivate(
        Organisation $organisation,
        Request $request
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(OrganisationDeactivationConfirmationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $organisation->deactivate();
            $this->eventService->saveOrganisation($organisation);
            $this->addFlash('success', 'Organisation deactivated.');
            return $this->redirectToRoute('events.dashboard');
        }
        $pageVars = [
            'pageTitle' => sprintf("Deactivate organisation '%s'", $organisation->getName()),
            'form' => $form,
        ];
        return $this->render('eventManager/organisations/edit.html.twig', $this->populatePageVars(
            pageVars: $pageVars,
            request: $request,
        ));
    }
}
