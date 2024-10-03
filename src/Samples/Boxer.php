<?php

declare(strict_types=1);

namespace CodefightsSdk\Samples;

use CodefightsSdk\Model\Area;
use CodefightsSdk\Model\IFighter;
use CodefightsSdk\Model\Move;

class Boxer implements IFighter
{
    private string $attack1;
    private string $attack2;
    private string $defence;

    private int $myScoreTotal = 0;
    private int $opponentScoreTotal = 0;
    private string $comment = "";

    public function __construct()
    {
        $this->attack1 = Area::NOSE;
        $this->attack2 = Area::JAW;
        $this->defence = Area::NOSE;
    }

    /**
     * Makes the next move for the Boxer.
     *
     * @param Move|null $opponentsLastMove The opponent's last move, if available.
     * @param int|null $myLastScore My last score, if available.
     * @param int|null $opponentsLastScore Opponent's last score, if available.
     *
     * @return \CodefightsSdk\model\Move The move the boxer makes.
     */
    public function makeNextMove(
        ?Move $opponentsLastMove = null,
        ?int $myLastScore = null,
        ?int $opponentsLastScore = null,
    ): Move {
        $move = new Move();

        $move->addAttack($this->attack1)
            ->addAttack($this->attack2)
            ->setComment('la la la');

        if ($opponentsLastMove !== null && in_array($this->defence, $opponentsLastMove->getAttacks())) {
            $move->setComment("Blocked your move to my $this->defence... hahaha");
        } else {
            $this->changeDefence();
        }

        $this->myScoreTotal += $myLastScore ?? 0;
        $this->opponentScoreTotal += $opponentsLastScore ?? 0;

        if ($this->myScoreTotal < $this->opponentScoreTotal) {
            $move->setComment('Okay, meat, me is mad now... going berserk');
            $move->addAttack(self::createRandomAttack());
        } else {
            $move->addBlock($this->defence);
        }

        return $move;
    }

    /**
     * Change the current defence area.
     */
    private function changeDefence(): void
    {
        $this->defence = $this->defence === Area::NOSE ? Area::JAW : Area::NOSE;
    }

    /**
     * Creates a random attack area.
     *
     * @return string The random attack area.
     */
    private static function createRandomAttack(): string
    {
        return rand(0, 10) >= 5 ? Area::GROIN : Area::BELLY;
    }
}
