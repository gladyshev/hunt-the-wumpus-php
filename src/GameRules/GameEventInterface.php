<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

interface GameEventInterface
{
    public function getPlayerId(): int;
}
