<?php

declare(strict_types=1);

namespace App\Dtos;

/**
 * @param int $count
 * @param int $totalSearchVolume
 * @param int $totalVisibilityScore
 */
class TotalVisibilityScoreDto
{
    public function __construct(
        public int $count,
        public int $totalSearchVolume,
        public float $totalVisibilityScore,
    ) {
    }
}
