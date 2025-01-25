<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetKeywordDataAction;
use App\Http\Requests\KeywordInsightRequest;
use App\Http\Resources\KeywordVisibilityResource;
use App\Http\Resources\TotalVisibilityResource;
use App\Services\DataForSeoApi\Exceptions\FailedApiResponseException;
use App\Services\DataForSeoApi\Exceptions\FixtureMissingException;
use App\Services\VisibilityScoreCalculator\VisibilityScoreCalculatorService;
use Illuminate\Http\Resources\Json\JsonResource;

class KeywordVisibilityController extends Controller
{
    /**
     * @param KeywordInsightRequest $request
     * @param GetKeywordDataAction $getKeywordDataAction
     * @param VisibilityScoreCalculatorService $visibilityScoreCalculatorService
     * @return JsonResource
     * @throws FailedApiResponseException
     * @throws FixtureMissingException
     */
    public function __invoke(
        KeywordInsightRequest $request,
        GetKeywordDataAction $getKeywordDataAction,
        VisibilityScoreCalculatorService $visibilityScoreCalculatorService,
    ): JsonResource {
        $keywords = $request->validated('keywords');
        $dtos = $getKeywordDataAction->handle($keywords);
        $keywordsVisibilityScore = $visibilityScoreCalculatorService->calculateKeywordsVisibilityScore($dtos);
        $totalVisibilityScore = $visibilityScoreCalculatorService->calculateTotalVisibilityScore($dtos);

        return JsonResource::make([
            'totals' => TotalVisibilityResource::make($totalVisibilityScore),
            'list' => KeywordVisibilityResource::collection($keywordsVisibilityScore),
        ]);
    }
}
