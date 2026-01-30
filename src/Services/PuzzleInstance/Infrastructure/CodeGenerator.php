<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Infrastructure;

use Random\RandomException;

class CodeGenerator
{

    private const string CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyz';

    private const int SLUG_LENGTH = 16;

    /**
     * @throws RandomException
     */
    public static function generateRandomCode(int $length = self::SLUG_LENGTH): string {
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= self::CHARACTERS[random_int(0, strlen(self::CHARACTERS) - 1)];
        }
        return $randomString;
    }
}
