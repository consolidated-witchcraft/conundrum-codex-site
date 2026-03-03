<?php

namespace App\DataFixtures;

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueType;
use App\Services\PuzzleTemplate\Infrastructure\PuzzleCategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClueTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * @var PuzzleCategoryRepository $repo
         */
        $repo = $manager->getRepository(CasebookSubjectClueType::class);
        $types = [
            'motive' => 'Motive',
            'whereabouts' => 'Whereabouts',
            'physical_details' => 'Physical Details',
            'timing' => 'Timing',
            'state_of_mind' => 'State of Mind',
            'background' => 'Background',
            'misc' => 'Misc'
        ];
        foreach ($types as $handle => $label) {
            if (!$repo->findByHandle($handle)) {
                $manager->persist(new CasebookSubjectClueType(label: $label, handle: $handle));
            }

        }

        $manager->flush();
    }
}
