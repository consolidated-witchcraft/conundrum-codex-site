<?php

declare(strict_types=1);

namespace App\Form\Mapper;

use App\Services\PuzzleInstance\Domain\Dynamic\Dto\DynamicPuzzleInstanceDto;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\UnmappableDynamicFieldTypeException;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;

class DynamicInstanceDataMapper implements DataMapperInterface
{

    /**
     * @param DynamicPuzzleInstanceDto $viewData
     * @param \Traversable $forms
     * @return void
     */
    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        dd($viewData);
    }

    /**
     * @param \Traversable $forms
     * @param DynamicPuzzleInstanceDto $viewData
     * @return void
     * @throws UnmappableDynamicFieldTypeException
     */
    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        if (empty($viewData)) {
            return;
        }

        foreach ($forms as $fieldName =>  $fieldDefinition) {
            $fieldData = $fieldDefinition->getData();

            if ($fieldData === null) {
                continue;
            }

            $viewData->setConfigFieldValue($fieldName, $fieldData);
        }
    }
}
