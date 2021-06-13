<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;

use Htw\GameRules\GameEventInterface;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjectInterface;

final class LeadRoomHazard implements GameEventInterface
{
    private WorldObjectInterface $hazard;
    private int $playerId;

    public function __construct(int $playerId, WorldObjectInterface $hazard)
    {
        $this->hazard = $hazard;
        $this->playerId = $playerId;
    }

    public function getHazard(): WorldObjectInterface
    {
        return $this->hazard;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }
}
