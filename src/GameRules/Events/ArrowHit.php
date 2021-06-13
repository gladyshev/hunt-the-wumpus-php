<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;


use Htw\GameRules\GameEventInterface;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjectInterface;

final class ArrowHit implements GameEventInterface
{
    private int $playerId;
    private WorldObjectInterface $worldObject;

    public function __construct(
        int $playerId,
        WorldObjectInterface $worldObject
    ) {
        $this->playerId = $playerId;
        $this->worldObject = $worldObject;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    public function getWorldObject(): WorldObjectInterface
    {
        return $this->worldObject;
    }
}
