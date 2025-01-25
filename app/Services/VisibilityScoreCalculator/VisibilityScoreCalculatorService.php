<?php

declare(strict_types=1);

namespace App\Services\VisibilityScoreCalculator;

use App\Dtos\KeywordApiDataDto;
use App\Dtos\KeywordVisibilityScoreDto;
use App\Dtos\TotalVisibilityScoreDto;

class VisibilityScoreCalculatorService
{
    /**
     * @param array<KeywordApiDataDto> $keywordDtos
     * @return array<KeywordVisibilityScoreDto>
     */
    public function calculateKeywordsVisibilityScore(array $keywordDtos): array
    {
        $result = [];
        foreach ($keywordDtos as $keywordDto) {
            $result[] = new KeywordVisibilityScoreDto(
                $keywordDto->keyword,
                $keywordDto->searchVolume,
                $keywordDto->googleSerp,
                $this->calculateVisibilityScore($keywordDto),
            );
        }

        return $result;
    }

    /**
     * @param array<KeywordApiDataDto> $keywordDtos
     * @return TotalVisibilityScoreDto
     */
    public function calculateTotalVisibilityScore(array $keywordDtos): TotalVisibilityScoreDto
    {
        $keywordsCount = count($keywordDtos);
        $totalSearchVolume = array_sum(data_get($keywordDtos, '*.searchVolume'));
        $totalVisibilityIndex = array_sum(
            array_map(fn(KeywordApiDataDto $dto) => $this->calculateVisibilityIndex($dto), $keywordDtos)
        );
        $totalVisibilityScore = $totalVisibilityIndex / $totalSearchVolume;

        return new TotalVisibilityScoreDto($keywordsCount, $totalSearchVolume, $totalVisibilityScore);
    }

    /*
     * @TODO this formula cancels out the visibility index (reverse formula)
     */
    private function calculateVisibilityScore(KeywordApiDataDto $keywordDto): float
    {
        return $this->calculateVisibilityIndex($keywordDto) / $keywordDto->searchVolume;
    }

    private function calculateVisibilityIndex(KeywordApiDataDto $keywordDto): float
    {
        return $keywordDto->searchVolume * $this->calculateClickThroughRate($keywordDto->googleSerp);
    }

    private function calculateClickThroughRate(?int $googleSerp): float
    {
        return match ($googleSerp) {
            1 => 1,
            2 => 0.95,
            3 => 0.90,
            4 => 0.75,
            5 => 0.70,
            6 => 0.65,
            7 => 0.60,
            8 => 0.55,
            9 => 0.50,
            10 => 0.45,
            11 => 0.28,
            12 => 0.26,
            13 => 0.24,
            14 => 0.22,
            15 => 0.20,
            16 => 0.18,
            17 => 0.16,
            18 => 0.14,
            19 => 0.12,
            20 => 0.10,
            default => 0.00,
        };
    }
}
