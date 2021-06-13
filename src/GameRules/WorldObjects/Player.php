<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\WorldObjects;

use Htw\GameRules\DieableWorldObjectInterface;
use Htw\GameRules\WorldObjectInterface;

final class Player implements WorldObjectInterface, DieableWorldObjectInterface
{
    private int $arrows;
    private string $name;
    private string $id;
    private bool $isDead = false;
    private bool $isGotWumpus = false;

    public function __construct(
        int $arrows,
        string $name,
        string $id
    ) {
        $this->arrows = $arrows;
        $this->name = $name;
        $this->id = $id;
    }

    public function hasArrow(): bool
    {
        return $this->arrows > 0;
    }

    public function decreaseArrow(): bool
    {
        $this->arrows--;

        return $this->hasArrow();
    }

    public function die(): void
    {
        $this->isDead = true;
    }

    public function gotWumpus(): void
    {
        $this->isGotWumpus = true;
    }

    public function getType(): string
    {
        return self::TYPE_PLAYER;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDead(): bool
    {
        return $this->isDead;
    }

    public function isGotWumpus(): bool
    {
        return $this->isGotWumpus;
    }

    public function isGameOver(): bool
    {
        return
            $this->isDead()
            || !$this->hasArrow()
            || $this->isGotWumpus()
        ;
    }
}
