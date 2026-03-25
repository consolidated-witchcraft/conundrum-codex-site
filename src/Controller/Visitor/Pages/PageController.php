<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Pages;

use App\Controller\AbstractBaseController;
use App\Form\Visitor\TestFormType;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractBaseController
{
    public function __construct(
        private readonly PuzzleTemplateServiceInterface $puzzleService,
        private readonly UserServiceInterface $userService,
        FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {
        parent::__construct($featureSwitcherService);
    }

    #[Route('/', name: 'app.pages.home')]
    public function index(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Welcome to the Conundrum Codex!',
            'templates' => $this->puzzleService->getTemplates()
        ];
        return $this->render('/visitor/pages/index.html.twig', $this->populatePageVars($pageVars, $request));
    }
    #[Route('/contributing', name: 'app.pages.contributing')]
    public function contributing(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Contributing',
        ];
        return $this->render('/visitor/pages/contributing.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/about', name: 'app.pages.about')]
    public function about(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'About',
        ];
        return $this->render('/visitor/pages/about.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/coc', name: 'app.pages.coc')]
    public function terms(Request $request): Response {
        $pageVars =[

            'pageTitle' => 'Code of Conduct',
        ];
        return $this->render('/visitor/pages/terms.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/credits', name: 'app.pages.credits')]
    public function credits(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Credits',
            'credits' => [
                [
                    'author' => 'Greg \'CRAOLA\' Simkins',
                    'contribution' => 'Various Art',
                    'link' => 'https://gregsimkinsart.com/',
                    'license' => 'https://creativecommons.org/licenses/by/4.0/'
                ],

                [
                    'author' => 'David Revoy',
                    'contribution' => 'Various Art',
                    'link' => 'https://www.davidrevoy.com/',
                    'license' => 'https://creativecommons.org/licenses/by/3.0/'
                ],
                [
                    'author' => 'Lorc',
                    'contribution' => 'Icons',
                    'link' => 'https://lorcblog.blogspot.com/',
                    'license' => 'https://creativecommons.org/licenses/by/3.0/'
                ],
                [
                    'author' => 'Betsy Ogilvie',
                    'link' => 'https://betsyogilvie.com/',
                    'contribution' => 'Various Art',
                    'license' => 'https://creativecommons.org/licenses/by/4.0/deed.en'
                ],
                [
                    'author' => 'SVGRepo',
                    'contribution' => 'Icons',
                    'link' => 'https://www.svgrepo.com/',
                    'license' => 'https://creativecommons.org/licenses/by/4.0/deed.en'
                ],
                [
                    'author' => 'Creative Cat and Co',
                    'link' => 'https://creativecatandco.com/',
                    'contribution' => 'Decorative Elements',
                    'license' => 'Commercial',
                ]
            ]
        ];
        return $this->render('/visitor/pages/credits.html.twig', $this->populatePageVars($pageVars, $request));
    }
    #[Route('/tutorial', name: 'app.pages.tutorial')]
    public function tutorial(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Tutorial',
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.tutorial',
                    'label' => 'Tutorial',
                    'active' => true
                ]
            ]
        ];
        return $this->render('/visitor/pages/tutorial.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/layout', name: 'app.pages.layout')]
    public function layout(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Layout',
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.layout',
                    'label' => 'Layout',
                    'active' => true
                ]
            ]
        ];
        return $this->render('/visitor/pages/layout.html.twig', $this->populatePageVars($pageVars, $request));
    }


    #[Route('/widgets', name: 'app.pages.widgets')]
    public function widgets(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Widgets',
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.widgets',
                    'label' => 'Widgets',
                    'active' => true
                ]
            ],
            'feats' => $this->userService->findAllFeats(),
        ];
        return $this->render('/visitor/pages/widgets.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/forms', name: 'app.pages.forms')]
    public function form(Request $request): Response {
        $form = $this->createForm(TestFormType::class);
        $pageVars =[
            'pageTitle' => 'Forms',
            'form' => $form,
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.layout',
                    'label' => 'Layout',
                    'active' => true
                ]
            ]
        ];
        return $this->render('/visitor/pages/form.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
