<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;

use Htw\GameRules\GameEventInterface;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjectInterface;

final class ArrowMissed implements GameEventInterface
{
    private int $playerId;

    public function __construct(int $playerId) {
        $this->playerId = $playerId;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }
}
