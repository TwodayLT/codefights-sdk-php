<?php

declare(strict_types=1);

namespace CodefightsSdk\Boilerplate\Server;

use CodefightsSdk\Model\Move;

class ServerResponse
{
    public ?Move $move = null;
    public ?int $score1 = null;
    public ?int $score2 = null;

    public function __construct(?Move $move = null, ?int $score1 = null, ?int $score2 = null)
    {
        $this->move = $move;
        $this->score1 = $score1;
        $this->score2 = $score2;
    }
}
