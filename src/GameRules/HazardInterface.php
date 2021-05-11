<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\GameRules;

interface HazardInterface
{
    public const TYPE_BAT = 'BAT';
    public const TYPE_PIT = 'PIT';
    public const TYPE_WUMPUS = 'WUMPUS';

    public function getType(): string;
}
