<?php

declare(strict_types=1);

namespace CodefightsSdk\Boilerplate\Server;

use CodefightsSdk\Boilerplate\ProtocolException;
use CodefightsSdk\Model\Area;
use CodefightsSdk\Model\Move;

class Protocol
{
	public const HANDSHAKE = "I-AM ready";
	public const REQUEST_HEADER = "";

	public const YOUR_SCORE = "YOUR-SCORE";
	public const OPPONENT_SCORE = "OPPONENT-SCORE";
	public const ENEMY_MOVE = "ENEMY-MOVE";
	public const MOVE_COMMENT = "COMMENT";

	private $outStream;
	private $inStream;

	public function __construct($inStream, $outStream)
	{
		$this->outStream = $outStream;
		$this->inStream = $inStream;
	}

	public function handshake(): void
	{
		fwrite($this->outStream, self::HANDSHAKE . PHP_EOL);
		fflush($this->outStream);
	}

	public function sendRequest(Move $move): void
	{
		fwrite($this->outStream, self::REQUEST_HEADER . self::serializeMove($move) . PHP_EOL);
		fflush($this->outStream);
	}

	public function readResponse(): ?ServerResponse
	{
		$line = fgets($this->inStream);
		return $line !== false ? self::parse(trim($line)) : null;
	}

	public static function serializeMove(Move $move): string
	{
		$result = '';

		foreach ($move->getAttacks() as $attack) {
			$result .= 'a' . $attack[0];
		}

		foreach ($move->getBlocks() as $block) {
			$result .= 'b' . $block[0];
		}

		if ($move->getComment() !== null) {
			$result .= 'c' . self::sanitizeComment($move->getComment());
		}

		return strtolower($result);
	}

	public static function parseMove(string $input): Move
	{
		if (empty($input)) {
			throw new ProtocolException("Input stream was closed");
		}

		$move = new Move();
		$index = 0;

		while ($index < strlen($input)) {
			$type = $input[$index++];

			match ($type) {
				'a' => $move->addAttack(self::getArea($input, $index++)),
				'b' => $move->addBlock(self::getArea($input, $index++)),
				'.', 'c' => $move->setComment(substr($input, $index)),
				' ', "\t" => null, // Continue the loop
				default => throw new ProtocolException('Unrecognized input: ' . $type),
			};
		}

		return $move;
	}

	public static function sanitizeComment(?string $comment): ?string
	{
		if ($comment === null) {
			return null;
		}

		$breaks = ["\t", "\n", "\""];
		$result = str_replace($breaks, " ", $comment);
		$result = trim($result);

		return strlen($result) > 150 ? substr($result, 0, 150) : $result;
	}

	protected static function parse(string $line): ServerResponse
	{
		$response = new ServerResponse();
		$words = explode(' ', $line);
		$index = 0;
		$wordCount = count($words);

		while ($index < $wordCount) {
			$firstKeyword = strtoupper($words[$index++]);
			$nextKeyword = $words[$index++] ?? '';

			match ($firstKeyword) {
				self::YOUR_SCORE => $response->score1 = intval($nextKeyword),
				self::OPPONENT_SCORE => $response->score2 = intval($nextKeyword),
				self::ENEMY_MOVE => $response->move = self::parseMove($nextKeyword),
				default => throw new ProtocolException("Invalid keyword: $firstKeyword. Syntax is [YOUR-SCORE area] [OPPONENT-SCORE area] [ENEMY-MOVE move]"),
			};
		}

		return $response;
	}

	private static function getArea(string $line, int $index): string
	{
		if ($index >= strlen($line)) {
			throw new ProtocolException('Must also specify attack/defence area!');
		}

		return match ($line[$index]) {
			'n' => Area::NOSE,
			'j' => Area::JAW,
			'b' => Area::BELLY,
			'g' => Area::GROIN,
			'l' => Area::LEGS,
			default => throw new ProtocolException('Unrecognized area: ' . $line[$index]),
		};
	}
}
