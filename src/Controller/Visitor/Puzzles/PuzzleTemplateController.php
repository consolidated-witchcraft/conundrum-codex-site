<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PuzzleTemplateController extends AbstractPuzzleController
{


    #[Route('/puzzles', name: 'app.templates.index')]
    public function index(Request $request): Response
    {
        $pageVars = [
            'pageTitle' => 'Puzzles',
            'puzzles' => [
                [
                    'label' => 'The Hammer of Tharmekhûl',
                    'description' => 'Reroute an ancient super-weapon\'s power supply, and smite your enemies!',
                    'route' => 'app.puzzles.hammer',
                    'active'=> false
                ],
                [
                    'label' => 'The Lightforge of Dormuid Fireglad',
                    'description' => 'Some security systems are designed to keep something out. This one keeps something in.',
                    'route' => 'app.puzzles.forge',
                    'active' => true
                ]

            ]
        ];
        return $this->render('/visitor/puzzles/index.html.twig', $this->populatePageVars($pageVars, $request));
    }



    #[Route('/puzzles/templates', name: 'app.puzzles.template.index')]
    public function templateIndex(Request $request): Response {

        $pageVars = [
            'pageTitle' => 'Puzzles and Tools',
            'templates' => $this->puzzleService->getTemplates(),
            'categories' => $this->puzzleService->getAllCategories(),
        ];
        return $this->render('/visitor/puzzles/templates/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/puzzles/templates/{templateSlug}', name: 'app.puzzles.template.show')]
    public function templateGet(string $templateSlug, Request $request): Response {
        $template = $this->puzzleService->getTemplateBySlug($templateSlug);

        if (!$template) {
            throw $this->createNotFoundException('Template not found');
        }
        $pageVars = [
            'pageTitle' => $template->getTitle(),
            'template' => $template,

            'breadcrumbs' => [
                [
                    'route' => 'app.puzzles.template.index',
                    'label' => 'Templates',
                    'active' => false
                ],
                [
                    'label' => $template->getTitle(),
                    'active' => true
                ]
            ]

        ];
        return $this->render('/visitor/puzzles/templates/template.html.twig', $this->populatePageVars($pageVars, $request));
    }






    #[Route('/puzzles/categories/{categorySlug}', name: 'app.puzzles.categorySlug.show')]
    public function categoryGet(string $categorySlug, Request $request): Response {
        $category = $this->puzzleService->getCategoryBySlug($categorySlug);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $pageVars = [
            'pageTitle' => $category->getLabel(),
            'templates' => $this->puzzleService->getTemplatesByCategory($category),

            'breadcrumbs' => [
                [
                    'route' => 'app.puzzles.template.index',
                    'label' => 'Templates',
                    'active' => false
                ],
                [
                    'label' => $category->getLabel(),
                    'active' => true
                ]
            ]

        ];
        return $this->render('/visitor/puzzles/templates/category.html.twig', $this->populatePageVars($pageVars, $request));
    }


}
