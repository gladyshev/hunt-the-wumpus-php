<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;

use Htw\GameRules\GameEventInterface;
use Htw\GameRules\Hazard;
use Htw\GameRules\WorldObjectInterface;

final class LeadRoomHazard implements GameEventInterface
{
    private WorldObjectInterface $hazard;

    public function __construct(WorldObjectInterface $hazard)
    {
        $this->hazard = $hazard;
    }

    /**
     * @return Hazard
     */
    public function getHazard(): WorldObjectInterface
    {
        return $this->hazard;
    }
}
