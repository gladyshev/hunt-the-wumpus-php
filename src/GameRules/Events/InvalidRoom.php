<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;

use Htw\GameRules\GameEventInterface;

final class InvalidRoom implements GameEventInterface
{
    private int $room;
    private int $playerId;

    public function __construct(int $playerId, int $room)
    {
        $this->room = $room;
        $this->playerId = $playerId;
    }

    /**
     * @return int
     */
    public function getRoom(): int
    {
        return $this->room;
    }

    /**
     * @return int
     */
    public function getPlayerId(): int
    {
        return $this->playerId;
    }
}