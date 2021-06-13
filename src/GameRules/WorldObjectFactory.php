<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;


use Htw\GameRules\WorldObjects\Bat;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjects\Wumpus;

final class WorldObjectFactory
{
    public function createByType(
        string $type,
        int $id
    ): WorldObjectInterface {

        switch ($type) {
            case WorldObjectInterface::TYPE_WUMPUS:
                return new Wumpus($id);

            case WorldObjectInterface::TYPE_BAT:
                return new Bat($id);

            case WorldObjectInterface::TYPE_PIT:
                return new Pit($id);
        }

        throw new \RuntimeException('UNSUPPORTED TYPE OF WORLD OBJECT');
    }
}
