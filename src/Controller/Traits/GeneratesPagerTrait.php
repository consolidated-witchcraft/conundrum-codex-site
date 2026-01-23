<?php

namespace App\Controller\Traits;

trait GeneratesPagerTrait
{
    private function generatePager(int $page, int $resultsPerPage, int $totalResults): array {
        $numPagesToGenerate = (int) ceil($totalResults / $resultsPerPage);
        $pager = [];
        $pager['pages'] =[];
        if ($numPagesToGenerate > 1) {
            if ($page > 1) {
                $pager['back'] = $page -1;
            }
            for ($i = 1; $i <= $numPagesToGenerate; $i++) {
                $pager['pages'][$i] = $i;
            }
            if ($numPagesToGenerate > $page) {
                $pager['next'] = $page +1;
            }
        }

        return $pager;
    }
}
