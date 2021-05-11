<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\GameRules;


interface DataStorageInterface
{
    public function get(string $key);
    public function set(string $key, $data);
}
