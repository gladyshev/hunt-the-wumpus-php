<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\WorldObjects;

use Htw\GameRules\ArrowHittableWorldObjectInterface;
use Htw\GameRules\CanDieWorldObjectInterface;
use Htw\GameRules\WorldObjectInterface;

final class Player implements
    WorldObjectInterface,
    ArrowHittableWorldObjectInterface,
    CanDieWorldObjectInterface
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


    public function gotWumpus(): void
    {
        $this->isGotWumpus = true;
    }

    public function getName(): string
    {
        return $this->name;
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

    /* World Object */

    public function getType(): string
    {
        return self::TYPE_PLAYER;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /* Arrow Hittable */

    public function willHit(): bool
    {
        return $this->isDead();
    }

    public function hit(): void
    {
        $this->die();
    }

    /* Can Die */

    public function die(): void
    {
        $this->isDead = true;
    }

    public function isDead(): bool
    {
        return $this->isDead;
    }
}
