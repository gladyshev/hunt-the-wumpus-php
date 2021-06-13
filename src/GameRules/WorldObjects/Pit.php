<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules\WorldObjects;

use Htw\GameRules\WorldObjectInterface;

final class Pit implements WorldObjectInterface
{
    private int $id;

    public function __construct(
        int $id
    ) {
        $this->id = $id;
    }

    public function getType(): string
    {
        return self::TYPE_PIT;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
