<?php

namespace Htw\GameRules;

use Htw\GameRules\WorldObjects\Player;

final class World
{
    private array $map;
    private array $worldObjectsMap;

    public function __construct(
        array $map,
        array $worldObjectsMap
    ) {
        $this->map = $map;
        $this->worldObjectsMap = $worldObjectsMap;
    }

    public function addWorldObject(
        int $room,
        WorldObjectInterface $object
    ) {
        if (!isset($this->map[$room])) {
            throw new \RuntimeException('INVALID ROOM');
        }

        if (isset($this->worldObjectsMap[$room])) {
            throw new \RuntimeException('ROOM IS NOT EMPTY');
        }

        $this->worldObjectsMap[$room] = $object;
    }

    public function roomHasObject(string $worldObjectType, int $room): bool
    {
        return
            isset($this->worldObjectsMap[$room])
            && $this->worldObjectsMap[$room]->getType() === $worldObjectType;
    }

    public function getRandomFreeRoom(): int
    {
        $free_rooms = array_diff(
            array_keys($this->map),
            array_keys($this->worldObjectsMap)
        );

        return $free_rooms[array_rand($free_rooms)];
    }

    public function getLeadRooms(int $room): array
    {
        if (!isset($this->map[$room])) {
            throw new \RuntimeException('NO ROOM ON MAP');
        }

        return $this->map[$room];
    }

    public function getRandomLeadRoom(int $room): int
    {
        $lead_rooms = $this->getLeadRooms($room);

        return $lead_rooms[
            array_rand($lead_rooms)
        ];
    }

    public function getNumRooms(): int
    {
        return max(array_keys($this->map));
    }

    public function existRoom(int $room): bool
    {
        return isset($this->map[$room]);
    }

    public function existTunnel(int $room1, int $room2): bool
    {
        return in_array($room2, $this->map[$room1]);
    }

    public function moveRoomObject(int $fromRoom, int $toRoom): void
    {
        $this->worldObjectsMap[$toRoom] = $this->worldObjectsMap[$fromRoom];
        unset($this->worldObjectsMap[$fromRoom]);
    }

    public function cleanRoom(int $room): void
    {
        unset($this->worldObjectsMap[$room]);
    }

    public function getPlayer(int $playerId): Player
    {
        $playerRoom = $this->getPlayerRoom($playerId);

        $player = $this->getRoomObject($playerRoom);

        if ($player instanceof Player) {
            return $player;
        }

        throw new \RuntimeException('PLAYER NOT FOUND');
    }

    public function getRoomObject(int $room): ?WorldObjectInterface
    {
        return $this->worldObjectsMap[$room] ?? null;
    }

    public function getPlayerRoom(int $playerId): int
    {
        foreach ($this->worldObjectsMap as $room => $object) {
            if (
                $object instanceof Player
                && $object->getId() === $playerId
            ) {
                return $room;
            }
        }

        throw new \RuntimeException('PLAYER NOT FOUND');
    }
}
