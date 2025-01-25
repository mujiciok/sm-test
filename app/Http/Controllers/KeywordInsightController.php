<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\KeywordInsightRequest;
use App\Models\KeywordInsight;
use App\Services\OpenAi\OpenAiService;
use Illuminate\Http\Resources\Json\JsonResource;

class KeywordInsightController extends Controller
{
    public function __invoke(KeywordInsightRequest $request, OpenAiService $openAiService): JsonResource
    {
        $hash = $request->validated('hash');
        $refreshInsight = (bool)$request->validated('refresh') === true;

        $keywordInsight = KeywordInsight::query()->where('hash', $hash)->first();
        $keywordsData = [
            'totals' => json_decode($keywordInsight->totals_data, true),
            'list' => $keywordInsight->keywords_data,
        ];

        $insight = $keywordInsight->insights_data;
        if (!$insight || $refreshInsight) {
            $insight = $openAiService->getInsight($keywordsData);

            $keywordInsight->update([
                'insights_data' => $insight,
            ]);
        }

        return JsonResource::make([
            'keywords_data' => $keywordInsight->keywords_data,
            'insight' => array_filter(explode(PHP_EOL, $insight)),
        ]);
    }
}
