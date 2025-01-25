<?php

declare(strict_types=1);

namespace App\Services\OpenAi;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAiService
{
    private const CHAT_MODEL = 'gpt-3.5-turbo';

    public function getInsight(array $keywordsData): string
    {
        $result = OpenAI::chat()->create([
            'model' => self::CHAT_MODEL,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $this->getUserPrompt() . json_encode($keywordsData, JSON_PRETTY_PRINT),
                ],
                [
                    'role' => 'developer',
                    'content' => $this->getDeveloperPrompt(),
                ]
            ],
            'temperature' => 0.8, // @TODO tweak temperature for randomness of result
        ]);

        //@TODO handle missing message or different response structure
        return $result->choices[0]->message->content;
    }

    private function getUserPrompt(): string
    {
        return 'I have a JSON object with some keywords data. '
            . 'Please provide 5 insights based on the visibility scores, search volumes, '
            . 'and any other metrics that you find usefull in this JSON: ';
    }

    private function getDeveloperPrompt(): string
    {
        return 'You are analyzing keyword data. Use the following guidelines to generate the insights: '
            . '1. Focus on keywords with high visibility scores as they are likely the most impactful. '
            . '2. Consider both visibility and search volume when ranking keywords by importance. '
            . '3. Look for keywords with an unusual combination of low search volume and high visibility, as they could present niche opportunities. '
            . '4. Mention any other metric or patterns you can derive that could provide actionable insights for SEO or marketing strategies. '
            . '5. Provide insights in a format that can easily be used for decision-making, ideally highlighting potential opportunities for optimization or new content.';
    }
}
