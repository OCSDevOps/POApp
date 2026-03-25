<?php

namespace App\Support;

use Illuminate\Support\Str;
use RuntimeException;

class StandardCostCodeCatalog
{
    private static ?array $catalog = null;

    public static function load(): array
    {
        if (self::$catalog !== null) {
            return self::$catalog;
        }

        $path = database_path('data/standard_cost_codes_march_2020.json');

        if (!is_file($path)) {
            throw new RuntimeException('The March 2020 standard cost code catalog file is missing.');
        }

        $decoded = json_decode(file_get_contents($path), true);

        if (!is_array($decoded) || !isset($decoded['codes']) || !is_array($decoded['codes'])) {
            throw new RuntimeException('The March 2020 standard cost code catalog file is invalid.');
        }

        self::$catalog = $decoded;

        return self::$catalog;
    }

    public static function metadata(): array
    {
        $catalog = self::load();

        return [
            'version' => $catalog['version'] ?? 'march-2020',
            'source_title' => $catalog['source_title'] ?? 'Standard Cost Code List',
            'source_company' => $catalog['source_company'] ?? null,
            'source_document_date' => $catalog['source_document_date'] ?? null,
            'source_local_reference' => $catalog['source_local_reference'] ?? null,
            'code_count' => (int) ($catalog['code_count'] ?? count($catalog['codes'])),
        ];
    }

    public static function codes(): array
    {
        return self::load()['codes'];
    }

    public static function codeCount(): int
    {
        return self::metadata()['code_count'];
    }

    public static function entryForCode(string $code): ?array
    {
        foreach (self::codes() as $row) {
            if (($row['code'] ?? null) === $code) {
                return $row;
            }
        }

        return null;
    }

    public static function segmentsFor(string $code): array
    {
        $segments = explode('-', $code);

        if (count($segments) !== 3) {
            throw new RuntimeException("Invalid standard cost code [{$code}] in catalog.");
        }

        return $segments;
    }

    public static function levelFor(string $code): int
    {
        [, $category, $detail] = self::segmentsFor($code);

        if ($category === '00' && $detail === '00') {
            return 1;
        }

        if ($detail === '00') {
            return 2;
        }

        return 3;
    }

    public static function templates(): array
    {
        $codes = self::codes();
        $metadata = self::metadata();
        $templateDefinitions = [
            [
                'key' => 'march-2020-full-catalog',
                'name' => 'March 2020 Standard - Full Catalog',
                'description' => sprintf(
                    'Complete %s tenant template pack sourced from %s (%d cost codes).',
                    self::displayVersionName($metadata['version']),
                    $metadata['source_title'],
                    $metadata['code_count']
                ),
                'codes' => array_values(array_map(static fn (array $row) => $row['code'], $codes)),
            ],
        ];

        foreach ($codes as $row) {
            $code = $row['code'] ?? null;

            if (!is_string($code) || !preg_match('/^\d-00-00$/', $code)) {
                continue;
            }

            $prefix = strtok($code, '-');
            $sectionName = self::formatSectionName((string) ($row['description'] ?? "Section {$prefix}"));
            $sectionCodes = array_values(array_map(
                static fn (array $item) => $item['code'],
                array_filter($codes, static fn (array $item) => Str::startsWith($item['code'] ?? '', "{$prefix}-"))
            ));

            $templateDefinitions[] = [
                'key' => "march-2020-section-{$prefix}",
                'name' => "March 2020 Standard - {$sectionName}",
                'description' => sprintf(
                    '%s section template from the March 2020 standard catalog (%d cost codes).',
                    $sectionName,
                    count($sectionCodes)
                ),
                'codes' => $sectionCodes,
            ];
        }

        return $templateDefinitions;
    }

    private static function displayVersionName(string $version): string
    {
        return Str::of($version)
            ->replace('-', ' ')
            ->title()
            ->value();
    }

    private static function formatSectionName(string $description): string
    {
        return Str::of(Str::lower($description))
            ->replace('&', ' & ')
            ->squish()
            ->title()
            ->value();
    }
}
