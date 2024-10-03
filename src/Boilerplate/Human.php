<?php

declare(strict_types=1);

namespace CodefightsSdk\Boilerplate;

use CodefightsSdk\Boilerplate\Server\Protocol;
use CodefightsSdk\Model\GameScoringRules;
use CodefightsSdk\Model\IFighter;
use CodefightsSdk\Model\Move;
use Exception;

class Human implements IFighter
{
	private $consoleOut;
	private $consoleIn;

	public function __construct()
	{
		$this->consoleOut = fopen("php://stdout", "w");
		$this->consoleIn = fopen("php://stdin", "r");
	}

	public function makeNextMove(?Move $oppMove = null, ?int $iScored = 0, ?int $oppScored = 0): Move
	{
		$this->printInstructions();

		while (true) {
			try {
				$userInput = trim(fgets($this->consoleIn));
				return self::parseInput($userInput);
			} catch (ProtocolException $ipe) {
				fwrite($this->consoleOut, "Human error: " . $ipe->getMessage() . PHP_EOL);
			} catch (Exception $oce) {
				fwrite($this->consoleOut, "Bye" . PHP_EOL);
				exit(0);
			}
		}
	}

	private function printInstructions(): void
	{
		fwrite($this->consoleOut, "Make your move by (A)ttacking and (B)locking (N)ose, (J)aw, (B)elly, (G)roin, (L)egs" . PHP_EOL);
		fwrite($this->consoleOut, "(for example, BN BB AN)" . PHP_EOL);
		fwrite($this->consoleOut, ": ");
	}

	private static function parseInput(string $input): Move
	{
		$input = str_replace(" ", "", $input);
		$input = strtolower($input);

		if (self::startsWith($input, "q")) {
			throw new Exception("Exiting");
		}

		$move = Protocol::parseMove($input);

		// Validate the move using game rules
		if (!GameScoringRules::isMoveLegal($move)) {
			throw new ProtocolException("Can make max 3 things at a time!");
		}

		return $move;
	}

	private static function startsWith(string $haystack, string $needle): bool
	{
		return str_starts_with($haystack, $needle);
	}
}
