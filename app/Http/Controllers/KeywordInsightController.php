<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\KeywordInsightRequest;
use App\Services\DataForSeoApi\Exceptions\FailedApiResponseException;
use App\Services\DataForSeoApi\Exceptions\FixtureMissingException;
use App\Services\DataForSeoApi\GoogleSerpLive;
use App\Services\DataForSeoApi\SearchVolumeLive;
use Illuminate\Http\JsonResponse;

class KeywordInsightController extends Controller
{
    /**
     * @param KeywordInsightRequest $request
     * @param SearchVolumeLive $searchVolumeLive
     * @param GoogleSerpLive $googleSerpLive
     * @return JsonResponse
     * @throws FailedApiResponseException
     * @throws FixtureMissingException
     */
    public function __invoke(
        KeywordInsightRequest $request,
        SearchVolumeLive $searchVolumeLive,
        GoogleSerpLive $googleSerpLive,
    ): JsonResponse {
        $keywords = $request->validated('keywords');
        $searchVolumeData = $searchVolumeLive
            ->setData('keywords', $keywords)
            ->request();
        $googleSerpData = $googleSerpLive
            ->setData('keywords', $keywords)
            ->request();

        return response()->json([
            'status' => 'success',
            'keywords' => $keywords,
            'searchVolumeData' => $searchVolumeData,
            'googleSerpData' => $googleSerpData,
        ]);
    }
}
