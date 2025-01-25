<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetKeywordDataAction;
use App\Actions\StoreKeywordInsightAction;
use App\Http\Requests\KeywordVisibilityRequest;
use App\Http\Resources\KeywordVisibilityResource;
use App\Http\Resources\TotalVisibilityResource;
use App\Services\DataForSeoApi\Exceptions\FailedApiResponseException;
use App\Services\DataForSeoApi\Exceptions\FixtureMissingException;
use App\Services\VisibilityScoreCalculator\VisibilityScoreCalculatorService;
use Illuminate\Http\Resources\Json\JsonResource;

class KeywordVisibilityController extends Controller
{
    /**
     * @param KeywordVisibilityRequest $request
     * @param GetKeywordDataAction $getKeywordDataAction
     * @param StoreKeywordInsightAction $storeKeywordInsightAction
     * @param VisibilityScoreCalculatorService $visibilityScoreCalculatorService
     * @return JsonResource
     * @throws FailedApiResponseException
     * @throws FixtureMissingException
     */
    public function __invoke(
        KeywordVisibilityRequest $request,
        GetKeywordDataAction $getKeywordDataAction,
        StoreKeywordInsightAction $storeKeywordInsightAction,
        VisibilityScoreCalculatorService $visibilityScoreCalculatorService,
    ): JsonResource {
        $keywords = $request->validated('keywords');
        $dtos = $getKeywordDataAction->handle($keywords);
        $keywordsVisibilityScore = $visibilityScoreCalculatorService->calculateKeywordsVisibilityScore($dtos);
        $totalVisibilityScore = $visibilityScoreCalculatorService->calculateTotalVisibilityScore($dtos);
        $storeKeywordInsightAction->handle($keywords, $keywordsVisibilityScore, $totalVisibilityScore);

        return JsonResource::make([
            'totals' => TotalVisibilityResource::make($totalVisibilityScore),
            'list' => KeywordVisibilityResource::collection($keywordsVisibilityScore),
        ]);
    }
}
