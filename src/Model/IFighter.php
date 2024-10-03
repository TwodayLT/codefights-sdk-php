<?php

declare(strict_types=1);

namespace CodefightsSdk\Model;

interface IFighter
{
	public function makeNextMove(?Move $opponentsLastMove, ?int $myLastScore, ?int $opponentsLastScore): Move;
}
