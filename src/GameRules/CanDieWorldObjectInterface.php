<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

interface CanDieWorldObjectInterface
{
    public function isDead(): bool;
    public function die(): void;
}
