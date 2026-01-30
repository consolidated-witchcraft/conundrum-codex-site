<?php

declare(strict_types=1);

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Form\Visitor\TestFormType;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractBaseController
{

    public function __construct(private PuzzleTemplateServiceInterface $puzzleService
    ) {
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
                    'author' => 'Lorc',
                    'contribution' => 'Icons',
                    'license' => 'https://creativecommons.org/licenses/by/3.0/'
                ],
                [
                    'author' => 'Betsy Ogilvy',
                    'contribution' => 'Various Art',
                    'license' => 'https://creativecommons.org/licenses/by/4.0/deed.en'
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
        return $this->render('/visitor/pages/admin-base.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/forms', name: 'app.pages.forms')]
    public function form(Request $request): Response {
        $form = $this->createForm(TestFormType::class);
        $pageVars =[
            'pageTitle' => 'Layout',
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
