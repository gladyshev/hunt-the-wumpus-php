<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\Storage;


final class FileDataStorage implements \Wumpus\GameRules\DataStorageInterface
{
    private string $filename;

    public function __construct(
        string $filename
    ) {
        $this->filename = $filename;
    }

    public function get(string $key, $default = null)
    {
        $raw_data = file_get_contents($this->filename);

        return \unserialize($raw_data)[$key] ?? $default;
    }

    public function set(string $key, $data)
    {
        // TODO: Implement set() method.
    }
}