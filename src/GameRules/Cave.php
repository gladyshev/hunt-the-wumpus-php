<?php

namespace Wumpus\GameRules;

final class Cave
{
    private array $map;
    private array $hazards;
    private array $hazard_placement;
    private array $players;

    public function __construct(
        array $map,
        array $hazards
    ) {
        $this->map = $map;
        $this->hazards = $hazards;

        $this->init();
    }

    public function init(): void
    {
        $this->hazard_placement = [];

        $rooms = array_keys($this->map);

        foreach ($this->hazards as $enemy) {
            $random_room = array_rand($rooms);
            unset($rooms[$random_room]);
            $this->hazard_placement[$random_room] = $enemy;
        }
    }

    public function roomHasHazard(string $hazard, int $room): bool
    {
        return isset($this->hazard_placement[$room])
            && $this->hazard_placement[$room] === $hazard;
    }

    public function getRandomFreeRoom(): int
    {
        $free_rooms = array_diff(
            array_keys($this->map),
            array_keys($this->hazard_placement)
        );

        return $free_rooms[array_rand($free_rooms)];
    }

    public function getLeadRooms(int $room): array
    {
        return $this->map[$room];
    }

    public function getRandomLeadRooms(int $room): int
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

    public function moveWumpusTo(int $from_room, int $to_room): void
    {
        unset($this->hazard_placement[$from_room]);

        $this->hazard_placement[$to_room] = 'wumpus';
    }
}