<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\Config;

final class FileConfig implements \Wumpus\GameRules\ConfigInterface
{
    private array $params;

    public function __construct(string $filename)
    {
        $this->params = require $filename;
    }

    public function getParam(string $param, $default = null)
    {
        return $this->params[$param] ?? $default;
    }
}
