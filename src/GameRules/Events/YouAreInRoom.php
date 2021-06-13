<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;

use Htw\GameRules\GameEventInterface;

final class YouAreInRoom implements GameEventInterface
{
    private int $room;
    private array $leadRooms;
    private int $playerId;

    public function __construct(int $playerId, int $room, array $leadRooms)
    {
        $this->room = $room;
        $this->leadRooms = $leadRooms;
        $this->playerId = $playerId;
    }

    public function getRoom(): int
    {
        return $this->room;
    }

    /**
     * @return int[]
     */
    public function getLeadRooms(): array
    {
        return $this->leadRooms;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }
}