<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;

use Htw\GameRules\GameEventInterface;

final class SuperBatSnatch implements GameEventInterface
{
    private int $playerId;
    private int $fromRoom;
    private int $toRoom;

    public function __construct(
        int $playerId,
        int $fromRoom,
        int $toRoom
    ) {
        $this->playerId = $playerId;
        $this->fromRoom = $fromRoom;
        $this->toRoom = $toRoom;
    }

    /**
     * @return int
     */
    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    /**
     * @return int
     */
    public function getFromRoom(): int
    {
        return $this->fromRoom;
    }

    /**
     * @return int
     */
    public function getToRoom(): int
    {
        return $this->toRoom;
    }
}