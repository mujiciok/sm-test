<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Dtos\KeywordVisibilityScoreDto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin KeywordVisibilityScoreDto
 */
class KeywordVisibilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->keyword,
            'position' => $this->googleSerp,
            'vscore' => $this->visibilityScore,
            'search_volume' => $this->searchVolume,
        ];
    }
}
