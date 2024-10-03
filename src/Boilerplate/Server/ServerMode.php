<?php

declare(strict_types=1);

namespace CodefightsSdk\Boilerplate\Server;

use CodefightsSdk\Model\IFighter;

class ServerMode
{
    private $inStream;
    private $outStream;
    private bool $cancelFlag = false;

    public function __construct()
    {
        $this->inStream = fopen("php://stdin", "r");
        $this->outStream = fopen("php://stdout", "w");
    }

    public function run(IFighter $fighter): void
    {
        $protocol = new Protocol($this->inStream, $this->outStream);
        $protocol->handshake();

        $resp = new ServerResponse();

        while (!$this->cancelFlag) {
            $move = $fighter->makeNextMove($resp->move, $resp->score1, $resp->score2);
            $protocol->sendRequest($move);
            $resp = $protocol->readResponse();
        }
    }

    public function setInputStream($inStream): void
    {
        $this->inStream = $inStream;
    }

    public function setOutputStream($outStream): void
    {
        $this->outStream = $outStream;
    }

    public function cancel(): void
    {
        $this->cancelFlag = true;
    }
}
