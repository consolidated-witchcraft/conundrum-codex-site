<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles;

use App\Dto\Visitor\Game\AddPuzzle\AddPuzzleStepOneDto;
use App\Dto\Visitor\Game\AddPuzzle\ChooseGameDto;
use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleConfigDto;
use App\Form\Type\DieRollType;
use App\Form\Visitor\Puzzle\AddPuzzle\ChooseGameType;
use App\Security\GameManagerVoter;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\PuzzleInstance;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\User\Domain\User;
use App\Validator\DiceRollConstraintValidator;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PuzzleTemplateController extends AbstractPuzzleController
{
    public const string ADD_TO_GAME_SESSION_KEY = 'add-to-game';

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
