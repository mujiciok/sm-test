<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeywordVisibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $this->hardcodeTestValues();

        return [
            'keywords' => 'required|array',
            'keywords.*' => 'required|string',
            // @TODO add individual keyword validation rules
        ];
    }

    /**
     * For testing purposes - allows GET request with "default" keywords
     * @return void
     */
    private function hardcodeTestValues(): void
    {
        if (request()->isMethod('GET')) {
            $this->query->set('keywords', [
                'topic seomonitor',
                'seo keyword monitor',
                'seo monitor login',
                'seo monitor forecast',
                'seo forecasting tool',
                'rank monitor',
                'seo rankings monitor',
                'serp features monitor',
                'seo forecasting',
                'agency rank tracking',
                'forecasting seo',
                'monitor keywords',
                'seo monitoring software',
                'rank tracker features',
                'daily rank tracker',
                'serp visibility',
                'seo monitoring tool',
                'seo visibility search metrics',
                'serp chrome extension',
                'track serp features',
                'what is rank tracking',
                'keyword forecasting',
                'rank tracker keyword difficulty',
                'seo visibility score',
                'serp metrics',
                'serp feature tracker',
            ]);
        }
    }
}
