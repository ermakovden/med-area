<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services;

use Application\AI\Recogniser\DTO\Responses\RecogniseAsyncResponse;
use Application\AI\Recogniser\DTO\Responses\RecognisedAnalysData;
use Domain\Analys\Enums\Analys;
use Domain\Analys\Enums\Unit;

/**
 * Service for parsing OCR response from Yandex Vision for analysis data
 *
 * Uses common regex pattern with enum values substitution
 */
class RecogniseResponseParser
{
    /**
     * Common pattern template: /\b({values})\b/iu
     */
    private const COMMON_PATTERN_TEMPLATE = '/\b(%s)\b/iu';

    /**
     * Pattern for unit values that may contain special characters: /({values})/iu
     * Using # delimiter to avoid conflicts with / in values like 'г/л'
     */
    private const UNIT_PATTERN_TEMPLATE = '#(%s)#iu';

    /**
     * Parse OCR response and extract analysis data with units
     *
     * @return list<RecognisedAnalysData>
     */
    public function parse(RecogniseAsyncResponse $response): array
    {
        $blocks = $response->getBlocks();

        if ($blocks === null) {
            return [];
        }

        $lines = $this->extractLines($blocks);

        if ($lines === []) {
            return [];
        }

        return $this->parseLines($lines);
    }

    /**
     * Extract text lines from blocks
     *
     * @param list<array<string, mixed>> $blocks
     * @return list<string>
     */
    private function extractLines(array $blocks): array
    {
        /** @var list<string> $lines */
        $lines = [];

        foreach ($blocks as $block) {
            /** @var array{lines?: mixed} $block */
            if (!isset($block['lines']) || !is_array($block['lines'])) {
                continue;
            }

            /** @var array{text?: mixed} $line */
            foreach ($block['lines'] as $line) {
                if (isset($line['text']) && is_string($line['text'])) {
                    $text = trim($line['text']);
                    if ($text !== '') {
                        $lines[] = $text;
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * Parse lines into analysis data with units
     * Looking for pairs: [analysis name] -> [value with unit]
     *
     * @param list<string> $lines
     * @return list<RecognisedAnalysData>
     */
    private function parseLines(array $lines): array
    {
        /** @var list<RecognisedAnalysData> $result */
        $result = [];
        /** @var non-empty-string|null */
        $currentAnalysName = null;
        /** @var Analys|null */
        $currentAnalysEnum = null;

        foreach ($lines as $line) {
            // Try to find analysis name
            $analysEnum = $this->findAnalys($line);

            if ($analysEnum !== null) {
                $currentAnalysName = $analysEnum->name;
                $currentAnalysEnum = $analysEnum;
                continue;
            }

            // If we have a current analysis and this line looks like a value
            if ($currentAnalysName !== null && $currentAnalysEnum !== null) {
                $parsedValue = $this->parseValueWithUnit($line);

                if ($parsedValue !== null) {
                    $result[] = RecognisedAnalysData::from([
                        'analys_id' => $currentAnalysEnum,
                        'name' => $currentAnalysName,
                        'data' => $parsedValue['data'],
                        'unit' => $parsedValue['unit'],
                    ]);

                    // Reset for next analysis
                    $currentAnalysName = null;
                    $currentAnalysEnum = null;
                }
            }
        }

        return $result;
    }

    /**
     * Find Analys enum by line text using common pattern
     * Builds pattern from all Analys enum case names
     */
    private function findAnalys(string $line): ?Analys
    {
        $pattern = $this->buildPatternFromEnum(Analys::class);

        if (preg_match($pattern, mb_strtolower($line), $matches)) {
            /** @var Analys|null */
            $result = $this->getEnumCaseByName(Analys::class, $matches[1]);
            return $result;
        }

        return null;
    }

    /**
     * Parse value and unit from text line using common pattern
     *
     * @return array{data: string, unit: Unit|null}|null
     */
    private function parseValueWithUnit(string $line): ?array
    {
        $unit = null;
        $data = $line;

        $pattern = $this->buildPatternFromEnum(Unit::class);
        if (preg_match($pattern, $line, $matches)) {
            /** @var Unit|null */
            $unit = $this->getEnumCaseByName(Unit::class, $matches[1]);
            /** @var string $data */
            $data = trim(preg_replace($pattern, '', $line) ?? '');
        }

        // Clean up data - remove extra whitespace and non-numeric characters
        /** @var string $data */
        $data = trim(preg_replace('/\s+/', ' ', $data) ?? '');

        // Extract only numeric values (including decimals and ranges like 40-60)
        if (preg_match('/^([\d.,\s-]+)$/u', $data, $matches)) {
            $data = trim($matches[1]);
        } else {
            // Try to extract numeric part from the beginning of the string
            if (preg_match('/^([\d.,\s-]+)/u', $data, $matches)) {
                $data = trim($matches[1]);
            }
        }

        if ($data === '') {
            return null;
        }

        return [
            'data' => $data,
            'unit' => $unit,
        ];
    }

    /**
     * Build regex pattern from enum case names
     *
     * @param class-string<\BackedEnum> $enumClass
     */
    private function buildPatternFromEnum(string $enumClass): string
    {
        /** @var list<string> $names */
        $names = array_map(
            static function ($case): string {
                // For Unit enum, use value (e.g., 'г/л', '%')
                // For Analys enum, use name (e.g., 'D3', 'B6')
                if ($case instanceof Unit) {
                    return preg_quote($case->value, '/');
                }
                return mb_strtolower($case->name);
            },
            $enumClass::cases()
        );

        // Use different pattern for Unit enum (without word boundaries)
        if ($enumClass === Unit::class) {
            return sprintf(self::UNIT_PATTERN_TEMPLATE, implode('|', $names));
        }

        return sprintf(self::COMMON_PATTERN_TEMPLATE, implode('|', $names));
    }

    /**
     * Get enum case by name or value (case-insensitive)
     *
     * @template T of \BackedEnum
     * @param class-string<T> $enumClass
     * @param string $nameOrValue
     * @return T|null
     */
    private function getEnumCaseByName(string $enumClass, string $nameOrValue): mixed
    {
        $searchLower = mb_strtolower($nameOrValue);

        foreach ($enumClass::cases() as $case) {
            // For Unit enum, compare by value (e.g., 'г/л', '%')
            // For Analys enum, compare by name (e.g., 'D3', 'B6')
            if ($case instanceof Unit) {
                if (mb_strtolower($case->value) === $searchLower) {
                    return $case;
                }
            } else {
                if (mb_strtolower($case->name) === $searchLower) {
                    return $case;
                }
            }
        }

        return null;
    }
}
