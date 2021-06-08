<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\Events;


final class OutOfArrows
{
    private int $playerId;

    public function __construct(int $playerId)
    {
        $this->playerId = $playerId;
    }

    /**
     * @return int
     */
    public function getPlayerId(): int
    {
        return $this->playerId;
    }
}