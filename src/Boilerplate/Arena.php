<?php

declare(strict_types=1);

namespace CodefightsSdk\Boilerplate;

use CodefightsSdk\Model\GameScoringRules;
use CodefightsSdk\Model\IFighter;

class Arena
{
    /** @var IFighter[] An array to hold registered fighters */
    private array $fighters = [];
    private Commentator $commentator;

    public function __construct()
    {
        $this->commentator = new Commentator();
    }

    public function registerFighter(IFighter $fighter, string $name): self
    {
        $this->fighters[$name] = $fighter;
        return $this;
    }

    public function stageFight(): void
    {
        if (count($this->fighters) !== 2) {
            throw new ProtocolException('Must be 2 fighters!');
        }

        $fighterNames = array_keys($this->fighters);
        $fighter1 = $this->fighters[$fighterNames[0]];
        $fighter2 = $this->fighters[$fighterNames[1]];
        $f1name = $fighterNames[0];
        $f2name = $fighterNames[1];

        $this->commentator->setFighterNames($f1name, $f2name);

        $f1Move = null;
        $f2Move = null;
        $score1 = 0;
        $score2 = 0;

        $f1Lifepoints = GameScoringRules::LIFEPOINTS;
        $f2Lifepoints = GameScoringRules::LIFEPOINTS;

        while ($f1Lifepoints > 0 && $f2Lifepoints > 0) {

            $move1 = $fighter1->makeNextMove($f2Move, $score1, $score2);
            $move2 = $fighter2->makeNextMove($f1Move, $score2, $score1);

            $score1 = GameScoringRules::calculateScore($move1->getAttacks(), $move2->getBlocks());
            $score2 = GameScoringRules::calculateScore($move2->getAttacks(), $move1->getBlocks());

            $this->commentator->describeRound($move1, $move2, $score1, $score2);

            $f1Lifepoints -= $score2;
            $f2Lifepoints -= $score1;

            $f1Move = $move1;
            $f2Move = $move2;
        }

        $this->commentator->gameOver($f1Lifepoints, $f2Lifepoints);
    }

    public function setCommentator(Commentator $c): self
    {
        $this->commentator = $c;
        return $this;
    }
}
