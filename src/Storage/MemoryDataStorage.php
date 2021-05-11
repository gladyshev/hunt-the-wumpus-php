<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\Storage;

final class MemoryDataStorage implements \Wumpus\GameRules\DataStorageInterface
{
    private $data = [];

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $data)
    {
        $this->data[$key] = $data;
    }
}
