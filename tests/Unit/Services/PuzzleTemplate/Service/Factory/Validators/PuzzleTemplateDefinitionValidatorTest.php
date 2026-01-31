<?php

namespace App\Tests\Unit\Services\PuzzleTemplate\Service\Factory\Validators;

use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\MissingRequiredTemplateFieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownFieldSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\PuzzleTemplateDefinitionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class PuzzleTemplateDefinitionValidatorTest extends TestCase
{
    private PuzzleTemplateDefinitionValidator $puzzleTemplateDefinitionValidator;
    public function setUp(): void {
        $this->puzzleTemplateDefinitionValidator = new PuzzleTemplateDefinitionValidator(
            $this->createStub(RouterInterface::class),
        );
    }
    public static function generateTestData(array $overrides = [], array $fieldKeysToUnset = []): array {
        $templateData = [
            'title' => $overrides['title'] ?? 'Test Title',
            'created' => $overrides['created'] ?? new \DateTime(),
            'slug' => $overrides['slug'] ?? 'test_slug',
            'description' => $overrides['description'] ?? 'Test Description',
            'categories' => $overrides['categories'] ?? [
                'category_1',
                'category_2'
            ],
            'type' => $overrides['type'] ?? 'misc',
            'static' => $overrides['static'] ?? false,
            'creator' => $overrides['creator'] ?? "test@test.dev",
            'configOptions' => $overrides['configOptions'] ?? [
            ],
            'elementName' => $overrides['elementName'] ?? 'test-element',
        ];
        foreach ($fieldKeysToUnset as $keyToUnset) {
            if (isset($templateData[$keyToUnset])) {
                unset($templateData[$keyToUnset]);
            }
        }
        return $templateData;
    }

    public static function provideValidateRequiredFieldsPresentTestData(): iterable {
        yield 'no_title' => [
            'missingFieldName' => 'title'
        ];

        yield 'no_created_date' => [
            'missingFieldName' => 'created'
        ];

        yield 'no_slug' => [
            'missingFieldName' => 'slug'
        ];

        yield 'no_description' => [
            'missingFieldName' => 'description'
        ];

        yield 'no_categories' => [
            'missingFieldName' => 'categories'
        ];

        yield 'no_type' => [
            'missingFieldName' => 'type'
        ];

        yield 'no_static' => [
            'missingFieldName' => 'static'
        ];
    }

    /**
     * @throws ConfigFieldTypeScalarMismatchException
     * @throws UnknownFieldSpecifiedException
     * @throws ConfigFieldTypeInvalidPatternException
     * @throws PuzzleTemplateRegistryBuildException
     */
    #[DataProvider('provideValidateRequiredFieldsPresentTestData')]
    public function testValidateRequiredFieldsPresent(
        string $missingFieldName,
    ): void {
        $templateData = self::generateTestData(fieldKeysToUnset: [$missingFieldName]);
        $moduleName = 'testmodule';
        $this->expectException(MissingRequiredTemplateFieldException::class,);
        $this->expectExceptionMessage(sprintf(MissingRequiredTemplateFieldException::ERROR_MESSAGE_PATTERN, $missingFieldName, $moduleName));
        $this->puzzleTemplateDefinitionValidator->validateJson($templateData, $moduleName);
    }

    public function testValidateAllFieldsKnown(): void {
        $templateData = self::generateTestData();
        $moduleName = 'testmodule';
        $templateData['unknownFieldName'] = 'unknownFieldName';
        $this->expectException(UnknownFieldSpecifiedException::class);
        $this->expectExceptionMessage("An unknown field named 'unknownFieldName' was discovered in the template.json for module 'testmodule'");
        $this->puzzleTemplateDefinitionValidator->validateJson($templateData, $moduleName);
    }
}
