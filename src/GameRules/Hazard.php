<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\GameRules;

final class Hazard implements \Wumpus\GameRules\HazardInterface
{
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
