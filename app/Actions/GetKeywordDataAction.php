<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\KeywordApiDataDto;
use App\Services\DataForSeoApi\Exceptions\FailedApiResponseException;
use App\Services\DataForSeoApi\Exceptions\FixtureMissingException;
use App\Services\DataForSeoApi\GoogleSerpLive;
use App\Services\DataForSeoApi\SearchVolumeLive;
use Illuminate\Support\Arr;

readonly class GetKeywordDataAction
{
    public function __construct(
        private SearchVolumeLive $searchVolumeLive,
        private GoogleSerpLive $googleSerpLive,
    ) {
    }

    /**
     * @param array<string> $keywords
     * @return array<KeywordApiDataDto>
     * @throws FailedApiResponseException
     * @throws FixtureMissingException
     */
    public function handle(array $keywords): array
    {
        $searchVolumeData = $this->searchVolumeLive
            ->setData('keywords', $keywords)
            ->request();
        $searchVolumePerKeyword = Arr::pluck($searchVolumeData, 'search_volume', 'keyword');
        $googleSerpData = $this->googleSerpLive
            ->setData('keywords', $keywords)
            ->request();
        $googleSerpPerKeyword = Arr::pluck($googleSerpData, 'items.*.rank_absolute', 'keyword');

        $dtos = [];
        foreach ($keywords as $keyword) {
            $searchVolume = $searchVolumePerKeyword[$keyword] ?? null;
            $googleSerp = $googleSerpPerKeyword[$keyword][0] ?? null; // get the first item's rank, as it is the highest
            $dtos[] = new KeywordApiDataDto($keyword, $searchVolume, $googleSerp);
        }

        return $dtos;
    }
}
