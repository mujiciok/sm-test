<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi;

use App\Services\DataForSeoApi\Enums\CountryCodeEnum;
use App\Services\DataForSeoApi\Enums\LanguageCodeEnum;
use App\Services\DataForSeoApi\Rules\KeywordsValidator;
use App\Services\DataForSeoApi\Rules\TargetValidator;

class GoogleSerpLive extends DataForSeoApiEndpoint
{
    /**
     * @TODO added wildcards * to get more data on SERP (without it, some keywords have no items in result)
     */
    private const DEFAULT_TARGET = '*seomonitor.com*';

    protected string $url = '/v3/serp/google/organic/live/regular';
    protected string $responsePath = 'tasks.*.result.*';
    protected array $validators = [
        KeywordsValidator::class,
        /**
         * @TODO location related validators
         * required field if you don’t specify location_name or location_coordinate
         */
        /**
         * @TODO language related validators
         * required field if you don’t specify language_name
         */
    ];
    protected string $fixturePath = 'data_for_seo_fixtures/google_serp.json';

    /**
     * @inheritDoc
     */
    protected function processRequest(): array
    {
        /*
         * @TODO CRITICAL not suitable for multiple keywords - too long request
         * use Task POST + Task Ready + Task GET (new GoogleSerp endpoint)
         */
        $data = [];
        foreach ($this->getData('keywords') as $keyword) {
            $postData = [[
                'location_code' => CountryCodeEnum::ROMANIA->value,
                'language_code' => LanguageCodeEnum::EN->value,
                'keyword' => $keyword,
                'target' => self::DEFAULT_TARGET,
            ]];
            $data[] = $this->getRequestData($postData);
        }

        return $data;
    }
}
