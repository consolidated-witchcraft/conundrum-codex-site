<?php

declare(strict_types=1);

namespace App\Services\PostalAddress\Domain\Factory;

use App\Services\PostalAddress\Domain\Exceptions\InvalidIsoCountryDataException;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountry;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountryList;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[AutoconfigureTag('kernel.cache_warmer')]
class IsoCountryListFactory implements CacheWarmerInterface
{
    private const string CACHE_KEY = 'countryCodeList';

    private const array KNOWN_FIELDS = [
        'name' => 'string',
        'alpha3' => 'string',
        'num' => 'int'
    ];
    private string $configDirectory;


    public function __construct(
        private readonly CacheInterface $cache,
        string $projectRootDirectory
    ) {
        $this->configDirectory = sprintf("%s/src/Services/PostalAddress/%s", $projectRootDirectory, 'Config');
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $this->buildService(true, false);
        return [];
    }

    public function buildService(bool $forceRefresh = false, bool $verbose = false): IsoCountryList {
        if ($forceRefresh) {
            $this->cache->delete(self::CACHE_KEY);
        }
        $countryCodeList = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use ($verbose) {
            /**
             * @var array<string, \App\Services\PostalAddress\Domain\ValueObjects\IsoCountry> $list
             */
            $list = [];
            $finder = new Finder();
            $finder->files()->in($this->configDirectory);
            if (!$finder->hasResults()) {
                throw new \Exception("Could not build postal codes country registry: country-codes.json is missing");
            }
            $fileContents = file_get_contents($this->configDirectory . DIRECTORY_SEPARATOR . 'country-codes.json');
            $fileContents = json_decode($fileContents, true);
            foreach ($fileContents as $countryCode => $countryData) {
                self::validateCountryData($countryCode, $countryData);
                $list[$countryCode] = new IsoCountry(
                    isoCountryCode: $countryCode,
                    alphaThree: $countryData['alpha3'],
                    name: $countryData['name'],
                    num: (int) $countryData['num'],
                );
            }

            return $list;
        });
        return new IsoCountryList(
            countryList: $countryCodeList
        );
    }

    public static function validateCountryData(string $countryCode, array $countryData): void {
        if (strlen($countryCode) !== 2) {
            throw new InvalidIsoCountryDataException(sprintf("%s is not a valid country code", $countryCode));
        }

        foreach (self::KNOWN_FIELDS as $requiredFieldName => $type) {
            if (!isset($countryData[$requiredFieldName])) {
                throw new InvalidIsoCountryDataException(sprintf("The data for %s is invalid. The %s field is missing", $countryCode, $requiredFieldName));
            }

            switch ($type) {
                case 'int':
                    if (!is_numeric($countryData[$requiredFieldName])) {
                        throw new InvalidIsoCountryDataException(sprintf("The data for %s is invalid. The %s field must be an integer", $countryCode, $requiredFieldName));
                    }
                case 'string':
                    if (!is_string($countryData[$requiredFieldName]) || empty($countryData[$requiredFieldName])) {
                        throw new InvalidIsoCountryDataException(sprintf("The data for %s is invalid. The %s field must be a non-blank string", $countryCode, $requiredFieldName));
                    }

            }
        }
    }
}
