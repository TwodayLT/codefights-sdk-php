<?php

declare(strict_types=1);

namespace CodefightsSdk\Model;

class Move
{
    private array $attacks = [];
    private array $blocks = [];
    private ?string $comment = null;

    public function getAttacks(): array
    {
        return $this->attacks;
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function addAttack(string $area): self
    {
        $this->attacks[] = $area;
        return $this;
    }

    public function addBlock(string $area): self
    {
        $this->blocks[] = $area;
        return $this;
    }

    public function __toString(): string
    {
        $result = 'Move';

        foreach ($this->attacks as $attack) {
            $result .= ' ATTACK ' . $attack;
        }

        foreach ($this->blocks as $block) {
            $result .= ' BLOCK ' . $block;
        }

        if ($this->comment !== null) {
            $result .= ' COMMENT ' . $this->comment;
        }

        return $result;
    }
}
