<?php

declare(strict_types=1);

namespace CodefightsSdk\Samples;

use CodefightsSdk\Model\Area;
use CodefightsSdk\Model\IFighter;
use CodefightsSdk\Model\Move;

class Kickboxer implements IFighter
{
    private string $attack1;
    private string $attack2;
    private string $defence;

    private string $opponentName = "";
    private string $comment = "";

    public function __construct()
    {
        $this->attack1 = Area::GROIN;
        $this->attack2 = Area::NOSE;
        $this->defence = Area::NOSE;
    }

    public function makeNextMove(
        ?Move $opponentsLastMove = null,
        ?int $iLost = null,
        ?int $iScored = null,
    ): Move {
        if ($opponentsLastMove !== null && in_array($this->defence, $opponentsLastMove->getAttacks())) {
            $this->comment = "Haha, blocked your attack to my $this->defence";
        } else {
            $this->comment = 'Ouch';
        }

        $this->attack2 = self::createRandomArea();

        if ($opponentsLastMove !== null && in_array($this->attack1, $opponentsLastMove->getBlocks())) {
            $this->attack1 = self::createRandomArea();
        }

        $move = new Move();
        $move->addAttack($this->attack1)
            ->addAttack($this->attack2)
            ->addBlock($this->defence)
            ->setComment($this->comment);

        return $move;
    }

    private static function createRandomArea(): string
    {
        $random = rand(0, 100);

        return match (true) {
            $random < 30 => Area::NOSE,
            $random < 70 => Area::JAW,
            $random < 90 => Area::GROIN,
            default => Area::LEGS,
        };
    }
}
