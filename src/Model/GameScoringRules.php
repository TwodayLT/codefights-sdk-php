<?php

declare(strict_types=1);

namespace CodefightsSdk\Model;

use Exception;

class GameScoringRules
{
    public const NOSE_SCORE = 10;
    public const JAW_SCORE = 8;
    public const BELLY_SCORE = 6;
    public const GROIN_SCORE = 4;
    public const LEGS_SCORE = 3;

    public const LIFEPOINTS = 150;

    public static function calculateScore(array $attacks, array $blocks): int
    {
        $score = 0;

        foreach ($attacks as $attack) {
            if (in_array($attack, $blocks)) {
                continue;
            }
            $score += self::getAttackSeverity($attack);
        }

        return $score;
    }

    public static function getAttackSeverity(string $attack): int
    {
        return match ($attack) {
            Area::NOSE => self::NOSE_SCORE,
            Area::JAW => self::JAW_SCORE,
            Area::GROIN => self::GROIN_SCORE,
            Area::BELLY => self::BELLY_SCORE,
            Area::LEGS => self::LEGS_SCORE,
            default => throw new Exception('Unknown attack vector: ' . $attack),
        };
    }

    public static function isMoveLegal(Move $move): bool
    {
        $totalActions = count($move->getAttacks()) + count($move->getBlocks());

        return $totalActions <= 3;
    }
}
