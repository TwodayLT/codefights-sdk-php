<?php

declare(strict_types=1);

namespace CodefightsSdk\Boilerplate;

use CodefightsSdk\Boilerplate\Server\Protocol;
use CodefightsSdk\Model\GameScoringRules;
use CodefightsSdk\Model\Move;

class Commentator
{
	private string $fighter1 = 'Fighter1';
	private string $fighter2 = 'Fighter2';

	private int $lp1 = GameScoringRules::LIFEPOINTS;
	private int $lp2 = GameScoringRules::LIFEPOINTS;

	public function setFighterNames(string $fighter1name, string $fighter2name): void
	{
		$this->fighter1 = $fighter1name;
		$this->fighter2 = $fighter2name;
	}

	public function describeRound(Move $move1, Move $move2, int $score1, int $score2): void
	{
		$this->describeMove($this->fighter1, $move1, $score1, $move2);
		$this->describeMove($this->fighter2, $move2, $score2, $move1);

		$this->lp1 -= $score2;
		$this->lp2 -= $score1;

		echo "$this->fighter1 vs $this->fighter2: $this->lp1 to $this->lp2" . PHP_EOL;
	}

	public function gameOver(int $f1Lifepoints, int $f2Lifepoints): void
	{
		echo 'FIGHT OVER' . PHP_EOL;

		if ($f1Lifepoints > $f2Lifepoints) {
			echo "THE WINNER IS $this->fighter1" . PHP_EOL;
		} elseif ($f2Lifepoints > $f1Lifepoints) {
			echo "THE WINNER IS $this->fighter2" . PHP_EOL;
		} else {
			echo "IT'S A DRAW!!!" . PHP_EOL;
		}
	}

	private function describeMove(string $fighterName, Move $move, int $score, Move $counterMove): void
	{
		echo $fighterName .
			self::describeAttacks($move, $counterMove, $score) .
			self::describeDefences($move) .
			self::describeComment($move) . PHP_EOL;
	}

	private static function describeAttacks(Move $move, Move $counterMove, int $score): string
	{
		$attacks = $move->getAttacks();

		if (count($attacks) <= 0) {
			return ' did NOT attack at all ';
		}

		$result = ' attacked ';

		foreach ($attacks as $attack) {
			$blocked = in_array($attack, $counterMove->getBlocks());
			$result .= $attack . ($blocked ? '(-), ' : '(+), ');
		}

		return $result . " scoring $score";
	}

	private static function describeDefences(Move $move): string
	{
		$blocks = $move->getBlocks();

		if (count($blocks) <= 0) {
			return ' and was NOT defending at all.';
		}

		$result = ' while defending ';

		foreach ($blocks as $block) {
			$result .= $block . ', ';
		}

		return rtrim($result, ', ');
	}

	private static function describeComment(Move $move): string
	{
		$comment = $move->getComment();

		if (empty($comment)) {
			return "";
		}

		return " Also said \"" . Protocol::sanitizeComment($comment) . "\"";
	}
}
