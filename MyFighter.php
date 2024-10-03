<?php

declare(strict_types=1);

namespace App;

require __DIR__ . '/vendor/autoload.php';

use CodefightsSdk\Boilerplate\SDK;
use CodefightsSdk\Model\Area;
use CodefightsSdk\Model\IFighter;
use CodefightsSdk\Model\Move;

class MyFighter implements IFighter
{
    public function makeNextMove(
        ?Move $opponentsLastMove = null,
        ?int $myLastScore = 0,
        ?int $opponentsLastScore = 0,
    ): Move {
        $move = new Move();

        $move->addAttack(Area::NOSE)
            ->addBlock(Area::GROIN)
            ->addAttack(Area::BELLY);

        return $move;
    }
}

SDK::run($argv);
