<?php

namespace App\Support;

class LevelCalculator
{
    /**
     * Simple level mapping from XP.
     * You can adjust thresholds anytime without touching Flutter.
     */
    public static function levelFromXp(int $xp): string
    {
        if ($xp < 100) return 'beginner';
        if ($xp < 300) return 'intermediate';
        if ($xp < 600) return 'advanced';
        if ($xp < 1000) return 'expert';
        return 'master';
    }
}
