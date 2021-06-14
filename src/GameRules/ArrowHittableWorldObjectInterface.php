<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

interface ArrowHittableWorldObjectInterface
{
    public function hit(): void;
    public function willHit(): bool;
}