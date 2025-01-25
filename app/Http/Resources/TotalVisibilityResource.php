<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Dtos\TotalVisibilityScoreDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TotalVisibilityScoreDto
 */
class TotalVisibilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_vscore' => $this->totalVisibilityScore,
            'count' => $this->count,
            'total_search_volume' => $this->totalSearchVolume,
        ];
    }
}
