<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

final class WorldFactory
{
    public function create(
        array $map,
        array $worldObjects
    ): World {
        $worldObjectPlacement = [];

        $freeRooms = array_keys($map);

        foreach ($worldObjects as $entity) {
            if (empty($freeRooms)) {
                throw new \RuntimeException('TOO MANY OBJECTS');
            }

            $random_room_key = array_rand($freeRooms);
            $random_room = $freeRooms[$random_room_key];

            unset($freeRooms[$random_room_key]);

            $worldObjectPlacement[$random_room] = $entity;
        }

        return new World($map, $worldObjectPlacement);
    }
}