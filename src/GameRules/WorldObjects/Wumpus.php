<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\WorldObjects;

use Htw\GameRules\DieableWorldObjectInterface;
use Htw\GameRules\WorldObjectInterface;

final class Wumpus implements WorldObjectInterface, DieableWorldObjectInterface
{
    private int $id;
    private bool $isDead;

    public function __construct(
        int $id,
        bool $isDead = false
    ) {
        $this->id = $id;
        $this->isDead = $isDead;
    }

    public function die(): void
    {
        $this->isDead = true;
    }

    public function isDead(): bool
    {
        return $this->isDead;
    }

    public function getType(): string
    {
        return self::TYPE_WUMPUS;
    }

    public function getId(): int
    {
        return $this->id;
    }
}