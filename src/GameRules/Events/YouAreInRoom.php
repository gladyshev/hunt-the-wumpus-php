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

    public function __construct(int $room, array $leadRooms)
    {
        $this->room = $room;
        $this->leadRooms = $leadRooms;
    }

    /**
     * @return int
     */
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
}