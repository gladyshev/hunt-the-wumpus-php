<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;

use Htw\GameRules\GameEventInterface;

final class LeadRoomWumpus implements GameEventInterface
{
    private int $playerId;

    public function __construct(int $playerId)
    {
        $this->playerId = $playerId;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }
}
