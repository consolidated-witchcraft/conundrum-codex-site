<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Worlds;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Worlds\UserWorldCreationDto;
use App\Dto\Visitor\Worlds\WorldCalendar\WorldCalendarSettingsDto;
use App\Form\Visitor\Worlds\WorldCalendarSettingsType;
use App\Form\Visitor\Worlds\WorldCreateType;
use App\Security\UserCanEditWorldVoter;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\User\Domain\User;
use App\Services\World\Domain\Exceptions\TooManyWorldCalendarMonthsException;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarDayOfWeek;
use App\Services\World\Domain\WorldCalendarMonth;
use App\Services\World\Service\Interfaces\WorldServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorldsController extends AbstractBaseController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly WorldServiceInterface $worldService,
        FeatureSwitcherServiceInterface $featureSwitcherService
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[Route('/worlds', name: 'app.worlds.index')]
    #[IsGranted('ROLE_USER')]
    #[IsGranted('FS_WORLDS_SYSTEM')]
    public function index(Request $request): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app.auth.login');
        }
        $pageVars = [
            'pageTitle' => 'My Worlds',
            'user' => $user,
        ];
        return $this->render('visitor/worlds/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/worlds/new', name: 'app.worlds.create')]
    #[IsGranted('ROLE_USER')]
    #[IsGranted('FS_WORLDS_SYSTEM')]
    public function addWorld(Request $request): Response {
        $dto = new UserWorldCreationDto();
        $form = $this->createForm(WorldCreateType::class, $dto);
        $form->handleRequest($request);
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app.auth.login');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $world = new World(
                name: $dto->name,
                description: $dto->description,
                slug: $dto->slug,
                creator: $user
            );
            $success = true;
            $violations = $this->validator->validate($world);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $success = false;
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->worldService->saveWorld($world);
                $this->addFlash('success', 'World created.');
                return $this->redirectToRoute('app.worlds.index');
            }
        }
        $pageVars = [
            'pageTitle' => 'Create a new World',
            'form' => $form
        ];
        return $this->render('visitor/worlds/add.html.twig', $this->populatePageVars($pageVars, $request));
    }

    /**
     * @throws TooManyWorldCalendarMonthsException
     */
    #[Route('/worlds/{worldSlug}/configure', name: 'app.worlds.configure')]
    #[IsGranted('ROLE_USER')]
    #[IsGranted('FS_WORLDS_SYSTEM')]
    #[IsGranted(UserCanEditWorldVoter::CONFIGURE_WORLD_ACTION, 'world')]
    public function configure(World $world, Request $request): Response {
        $dto = WorldCalendarSettingsDto::makeFromWorldCalendarSettings($world->getWorldCalendarSettings());
        $form = $this->createForm(WorldCalendarSettingsType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $calendarSettings = $world->getWorldCalendarSettings();
            $calendarSettings->setHoursInDay($dto->hoursInDay);
            foreach ($dto->daysOfWeek as $dayOfWeekDto) {
                if ($dayOfWeekDto->id === null) {
                    $mappedDayOfWeek = WorldCalendarDayOfWeek::makeFromDto($calendarSettings, $dayOfWeekDto);
                    $calendarSettings->addDayOfWeek($mappedDayOfWeek);
                } else {
                    $dayOfWeekId = $dayOfWeekDto->id;
                    $day = $calendarSettings->getDaysOfWeek()->findFirst(
                        function (int $key, WorldCalendarDayOfWeek $day) use ($dayOfWeekId) {
                            return $day->getId() === $dayOfWeekId;
                        }
                    );
                    $day->setName($dayOfWeekDto->name);
                    $day->setAbbreviation($dayOfWeekDto->abbreviation);
                }

            }

            foreach ($dto->months as $monthDto) {
                if ($monthDto->id === null) {
                    $mappedMonth = WorldCalendarMonth::makeFromDto($calendarSettings, $monthDto);
                    $calendarSettings->addWorldCalendarMonth($mappedMonth);
                } else {
                    $monthId = $monthDto->id;
                    $month = $calendarSettings->getWorldCalendarMonths()->findFirst(
                        function (int $key, WorldCalendarMonth $month) use ($monthId) {
                            return $month->getId() === $monthId;
                        }
                    );
                    $month->setName($monthDto->name);
                    $month->setAbbreviation($monthDto->abbreviation);
                }

            }
            $this->addFlash('success', 'World settings saved.');
            $this->worldService->saveWorld($world);

        }
        $pageVars = [
            'pageTitle' => 'Configure World',
            'calendarForm' => $form
        ];
        return $this->render('visitor/worlds/configure.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
