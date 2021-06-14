<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\WorldObjects;

use Htw\GameRules\ArrowHittableWorldObjectInterface;
use Htw\GameRules\CanDieWorldObjectInterface;
use Htw\GameRules\WorldObjectInterface;

final class Wumpus implements
    WorldObjectInterface,
    ArrowHittableWorldObjectInterface,
    CanDieWorldObjectInterface
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

    public function hit(): void
    {
        $this->die();
    }

    public function willHit(): bool
    {
        return $this->isDead();
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