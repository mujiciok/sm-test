<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi;

use App\Services\DataForSeoApi\Rules\KeywordsValidator;

class SearchVolumeLive extends DataForSeoApiEndpoint
{
    protected string $url = '/v3/keywords_data/google_ads/search_volume/live';
    protected string $responsePath = 'tasks.*.result';
    protected array $validators = [
        KeywordsValidator::class,
    ];
    protected string $fixturePath = 'data_for_seo_fixtures/search_volume.json';

    /**
     * @inheritDoc
     */
    protected function processRequest(): array
    {
        $postData = [['keywords' => $this->data['keywords'],]];

        return $this->getRequestData($postData);
    }
}
