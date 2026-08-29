<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Characters;

use App\Controller\AbstractBaseController;
use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\Visitor\User\CharacterDto;
use App\Form\Visitor\Account\Character\CreateCharacterForm;
use App\Form\Visitor\Account\Character\DeleteCharacterForm;
use App\Form\Visitor\Account\Character\EditCharacterForm;
use App\Security\UserOwnsCharacterVoter;
use App\Services\Character\Domain\Character;
use App\Services\Character\Service\Interfaces\CharacterServiceInterface;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use App\Services\User\Domain\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CharacterController extends AbstractBaseController
{
    use HandlesFileUploadsTrait;

    public function __construct(
        private readonly CharacterServiceInterface $characterService,
        protected readonly ManagedImageServiceInterface $managedImageService,
        #[Autowire('%kernel.project_dir%/public/uploads/images')]
        private readonly string $portraitImagesDirectory,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct(featureSwitcherService: $featureSwitcherService);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/account/characters/create', name: 'app.user.character.create')]
    public function create(
        Request $request,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to create a character.');
        }

        $dto = new CharacterDto();
        $form = $this->createForm(CreateCharacterForm::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($dto->portrait === null) {
                throw new \LogicException('A valid character portrait is required.');
            }

            $character = new Character(
                name: $dto->name,
                user: $user,
                class: $dto->class,
                race: $dto->race,
                portrait: $this->handleImageUpload(
                    uploadedFile: $dto->portrait,
                    destination: $this->portraitImagesDirectory,
                ),
                biography: $dto->biography,
            );

            $this->characterService->save(character: $character);
            $this->addFlash(type: 'success', message: 'Character created.');

            return $this->redirectToRoute(route: 'app.user.account');
        }

        return $this->render(
            view: 'visitor/character/create.html.twig',
            parameters: $this->populatePageVars([
                'pageTitle' => 'Create a Character',
                'form' => $form,
            ], $request),
        );
    }

    #[IsGranted('ROLE_USER')]
    #[IsGranted(UserOwnsCharacterVoter::MANAGE, 'character')]
    #[Route('/account/characters/{characterId}/edit', name: 'app.user.character.edit')]
    public function edit(Character $character, Request $request): Response
    {
        $dto = CharacterDto::makeFromCharacter(character: $character);
        $form = $this->createForm(EditCharacterForm::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character->setName(name: $dto->name);
            $character->setClass(class: $dto->class);
            $character->setRace(race: $dto->race);
            $character->setBiography(biography: $dto->biography);

            if ($dto->portrait !== null) {
                $character->setPortrait(portrait: $this->handleImageUpload(
                    uploadedFile: $dto->portrait,
                    destination: $this->portraitImagesDirectory,
                    imageToReplace: $character->getPortrait(),
                ));
            }

            $this->characterService->save(character: $character);
            $this->addFlash(type: 'success', message: 'Character updated.');

            return $this->redirectToRoute(route: 'app.user.account');
        }

        return $this->render(
            view: 'visitor/character/edit.html.twig',
            parameters: $this->populatePageVars([
                'pageTitle' => sprintf('Edit %s', $character->getName()),
                'character' => $character,
                'form' => $form,
            ], $request),
        );
    }

    #[IsGranted('ROLE_USER')]
    #[IsGranted(UserOwnsCharacterVoter::MANAGE, 'character')]
    #[Route('/account/characters/{characterId}/delete', name: 'app.user.character.delete')]
    public function delete(Character $character, Request $request): Response
    {
        $form = $this->createForm(DeleteCharacterForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->characterService->delete(character: $character);
            $this->addFlash(type: 'success', message: 'Character deleted.');

            return $this->redirectToRoute(route: 'app.user.account');
        }

        return $this->render(
            view: 'visitor/character/delete.html.twig',
            parameters: $this->populatePageVars([
                'pageTitle' => sprintf('Delete %s?', $character->getName()),
                'character' => $character,
                'form' => $form,
            ], $request),
        );
    }
}
