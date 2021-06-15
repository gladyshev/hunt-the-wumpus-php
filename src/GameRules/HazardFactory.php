<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;


use Htw\GameRules\WorldObjects\Bat;
use Htw\GameRules\WorldObjects\Pit;
use Htw\GameRules\WorldObjects\Wumpus;

final class HazardFactory
{
    public function createByType(
        string $type,
        ?int $id = null
    ): WorldObjectInterface {

        $objectId = $id ?? rand(100, getrandmax());

        switch ($type) {
            case WorldObjectInterface::TYPE_WUMPUS:
                return new Wumpus($objectId);

            case WorldObjectInterface::TYPE_BAT:
                return new Bat($objectId);

            case WorldObjectInterface::TYPE_PIT:
                return new Pit($objectId);
        }

        throw new \RuntimeException('UNSUPPORTED TYPE OF WORLD OBJECT');
    }
}
