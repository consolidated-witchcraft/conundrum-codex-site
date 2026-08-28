<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators;


use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateModuleStructureValidationException;

/**
 * Validates that all necessary files are present in the directory and have the correct file types.
 */
class PuzzleTemplateModuleStructureValidator
{
    private const array REQUIRED_FILES = [
        'template.json' => 'application/json'
    ];

    /**
     * @throws PuzzleTemplateModuleStructureValidationException
     */
    public function validate(string $moduleDirectoryName, string $moduleName): void
    {

        $moduleDirectoryPath = sprintf("%s/%s", $moduleDirectoryName, $moduleName);
        foreach (self::REQUIRED_FILES as $fileName => $expectedMimeType) {

            $filePath = sprintf("%s/%s", $moduleDirectoryPath, $fileName);

            if (!$this->fileExists($filePath)) {
                throw new PuzzleTemplateModuleStructureValidationException(
                    sprintf("The module directory '%s' does not contain the required file '%s'.", $moduleDirectoryPath, $fileName)
                );
            } elseif ($expectedMimeType !== $this->getFileMimeType($filePath)) {
                throw new PuzzleTemplateModuleStructureValidationException(
                    sprintf("The module directory '%s' contains the required file '%s', but it does not have the expected mime type %s.", $moduleDirectoryPath, $fileName, $expectedMimeType)
                );
            }


        }
    }

    private function getFileMimeType(string $filePath): string {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($fileInfo === false) {
            throw new PuzzleTemplateModuleStructureValidationException('Could not initialize MIME-type detection.');
        }

        $mimeType = finfo_file($fileInfo, $filePath);
        if ($mimeType === false) {
            throw new PuzzleTemplateModuleStructureValidationException(sprintf('Could not detect the MIME type of "%s".', $filePath));
        }

        return $mimeType;
    }

    private function fileExists(string $filePath): bool {
        return file_exists($filePath);
    }
}
