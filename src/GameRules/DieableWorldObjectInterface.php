<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

interface DieableWorldObjectInterface
{
    public function isDead(): bool;
    public function die(): void;
}