<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\KeywordInsight
 *
 * @property int $id
 * @property string $hash
 * @property array<array-key, mixed> $keywords
 * @property array<array-key, mixed> $keywords_data
 * @property string $totals_data
 * @property array<array-key, mixed>|null $insights_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|KeywordInsight newModelQuery()
 * @method static Builder<static>|KeywordInsight newQuery()
 * @method static Builder<static>|KeywordInsight query()
 * @method static Builder<static>|KeywordInsight whereCreatedAt($value)
 * @method static Builder<static>|KeywordInsight whereHash($value)
 * @method static Builder<static>|KeywordInsight whereId($value)
 * @method static Builder<static>|KeywordInsight whereInsightsData($value)
 * @method static Builder<static>|KeywordInsight whereKeywords($value)
 * @method static Builder<static>|KeywordInsight whereKeywordsData($value)
 * @method static Builder<static>|KeywordInsight whereTotalsData($value)
 * @method static Builder<static>|KeywordInsight whereUpdatedAt($value)
 * @mixin Eloquent
 */
class KeywordInsight extends Model
{
    protected $fillable = [
        'hash',
        'keywords',
        'keywords_data',
        'totals_data',
        'insights_data',
    ];

    protected $casts = [
        'keywords' => 'array',
        'keywords_data' => 'array',
        'total_data' => 'array',
        'insights_data' => 'array',
    ];

    /**
     * @TODO move out of the model
     * @param array $keywords
     * @return string
     */
    public static function createHash(array $keywords): string
    {
        sort($keywords);

        return md5(serialize($keywords));
    }
}
