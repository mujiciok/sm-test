<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\KeywordVisibilityScoreDto;
use App\Dtos\TotalVisibilityScoreDto;
use App\Http\Resources\KeywordVisibilityResource;
use App\Http\Resources\TotalVisibilityResource;
use App\Models\KeywordInsight;

class StoreKeywordInsightAction
{
    /**
     * @param array<string> $keywords
     * @param array<KeywordVisibilityScoreDto> $keywordsVisibilityScore
     * @param TotalVisibilityScoreDto $totalVisibilityScore
     * @return KeywordInsight
     */
    public function handle(
        array $keywords,
        array $keywordsVisibilityScore,
        TotalVisibilityScoreDto $totalVisibilityScore
    ): KeywordInsight {
        // @TODO get rid of ->toArray(request())
        return KeywordInsight::query()->updateOrCreate(
            [
                'hash' => KeywordInsight::createHash($keywords),
            ],
            [
                'keywords' => $keywords,
                'keywords_data' => KeywordVisibilityResource::collection($keywordsVisibilityScore)->toArray(request()),
                'totals_data' => TotalVisibilityResource::make($totalVisibilityScore)->toArray(request()),
            ],
        );
    }
}
