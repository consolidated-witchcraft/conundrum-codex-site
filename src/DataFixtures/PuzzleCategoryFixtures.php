<?php

namespace App\DataFixtures;

use App\Services\PuzzleTemplate\Domain\PuzzleCategory;
use App\Services\PuzzleTemplate\Infrastructure\PuzzleCategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PuzzleCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * @var PuzzleCategoryRepository $repo
         */
        $repo = $manager->getRepository(PuzzleCategory::class);
        $puzzleCategories = [
            'switch' => [
                'label' => 'Switch',
                'description' => 'In switch puzzles, a party must set a bank of switches in the correct configuration.',
            ],
            'button' => [
                'label' => 'Button',
                'description' => 'In button puzzles, a party must press buttons in a particular order, with immediate consequences for failure.'
            ],
            'logic' => [
                'label' => 'Logic',
                'description' => 'In a logic puzzle, players must use clues to make logical deductions about a fact. '
            ],
            'riddle' => [
                'label' => 'Riddles',
                'description' => 'In a riddle/wordplay puzzle, players must answer a question correctly'
            ]
        ];
        foreach ($puzzleCategories as $handle => $definition) {
            if (!$repo->findByHandle($handle)) {
                $manager->persist(new PuzzleCategory(
                    label: $definition['label'],
                    handle: $handle,
                    description: $definition['description']
                ));
            }

        }

        $manager->flush();
    }
}
