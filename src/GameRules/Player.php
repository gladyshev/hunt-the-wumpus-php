<?php
/**
 * @project Hunt the Wampus
 */

namespace Wumpus\GameRules;

final class Player
{
    private int $room;
    private int $arrows;

    public function __construct(
        int $room,
        int $arrows
    ) {
        $this->room = $room;
        $this->arrows = $arrows;
    }

    public function shoot(): void
    {
        $this->arrows--;
    }

    public function move(int $room): void
    {
        $this->room = $room;
    }

    public function getRoom(): int
    {
        return $this->room;
    }

    public function hasArrow(): bool
    {
        return $this->arrows > 0;
    }
}