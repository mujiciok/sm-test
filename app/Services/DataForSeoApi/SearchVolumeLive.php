<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi;

use App\Services\DataForSeoApi\Rules\KeywordsValidator;

class SearchVolumeLive extends DataForSeoApiEndpoint
{
    protected string $url = '/v3/keywords_data/google_ads/search_volume/live';
    protected string $fixturePath = 'search_volume.json';
    protected array $validators = [
        KeywordsValidator::class,
    ];

    public function request(): array
    {
        $this->validate();
        $postData[] = [
            'keywords' => $this->data['keywords'],
        ];

        return $this->getRequestData($postData);
    }
}
