<?php

declare(strict_types=1);

namespace CodefightsSdk\Boilerplate;

use CodefightsSdk\Boilerplate\Human;
use CodefightsSdk\Boilerplate\server\ServerMode;
use CodefightsSdk\Samples\Boxer;
use CodefightsSdk\Samples\Kickboxer;
use App\MyFighter;

class SDK
{
	public const FIGHT_HUMAN_SWITCH = "--fight-me";
	public const FIGHT_BOT_SWITCH = "--fight-bot";
	public const RUN_ON_SERVER_SWITCH = "--fight-on-Server";

	public const USAGE_INSTRUCTIONS = self::FIGHT_HUMAN_SWITCH . "\t\truns your bot against you in interactive mode\n" .
	self::FIGHT_BOT_SWITCH . " boxer\truns your bot against a built-in boxer bot\n" .
	self::FIGHT_BOT_SWITCH . " kickboxer\truns your bot against a built-in kickboxer bot\n" .
	self::RUN_ON_SERVER_SWITCH . "\truns your bot in codefights engine environment\n";

	public static function run(array $argv): void
	{
		array_splice($argv, 0, 1);

		$mode = match (true) {
			self::isFightHumanMode($argv) => 'fightHuman',
			self::isFightBotMode($argv) => 'fightBot',
			self::isRunInServerMode($argv) => 'runServer',
			default => 'printUsage',
		};

		self::executeMode($mode, $argv);
	}

	private static function executeMode(string $mode, array $argv): void
	{
		match ($mode) {
			'fightHuman' => self::runFightHuman(),
			'fightBot' => self::runFightBot($argv),
			'runServer' => self::runServer(),
			'printUsage' => self::printUsageInstructions($argv),
			default => throw new ProtocolException("Invalid mode: $mode"),
		};
	}

	private static function runFightHuman(): void
	{
		$arena = new Arena();
		$arena->registerFighter(new Human(), "You")
			->registerFighter(new MyFighter(), "Your bot");
		$arena->stageFight();
	}

	private static function runFightBot(array $argv): void
	{
		$arena = new Arena();
		$arena->registerFighter(new MyFighter(), "Your bot")
			->registerFighter(self::createBot($argv), $argv[1]);
		$arena->stageFight();
	}

	private static function runServer(): void
	{
		$serverMode = new ServerMode();
		$serverMode->run(new MyFighter());
	}

	private static function isRunInServerMode(array $args): bool
	{
		return count($args) === 1 && strcasecmp($args[0], self::RUN_ON_SERVER_SWITCH) === 0;
	}

	private static function isFightBotMode(array $args): bool
	{
		return count($args) >= 2 && strcasecmp($args[0], self::FIGHT_BOT_SWITCH) === 0;
	}

	private static function isFightHumanMode(array $args): bool
	{
		return count($args) === 1 && strcasecmp($args[0], self::FIGHT_HUMAN_SWITCH) === 0;
	}

	private static function printUsageInstructions(array $args): void
	{
		if (count($args) > 0) {
			echo 'unrecognized option(s): ' . implode(' ', $args) . PHP_EOL;
		}
		echo self::USAGE_INSTRUCTIONS . PHP_EOL;
	}

	private static function createBot(array $args): Boxer|Kickboxer
	{
		return match (strtolower($args[1])) {
			'boxer' => new Boxer(),
			'kickboxer' => new Kickboxer(),
			default => throw new ProtocolException("Unrecognized built-in bot: {$args[1]}"),
		};
	}
}
