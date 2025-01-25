<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\KeywordInsight;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeywordInsightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $this->hardcodeTestValues();

        return [
            'hash' => [
                'required',
                Rule::exists('keyword_insights', 'hash'),
            ],
            'refresh' => [
                'nullable',
                'boolean',
            ]
        ];
    }

    /**
     * For testing purposes - allows GET request with latest "hash" from DB
     * @return void
     */
    private function hardcodeTestValues(): void
    {
        if (request()->isMethod('GET')) {
            $this->query->set('hash', KeywordInsight::query()->latest()->first()->hash);
        }
    }
}
