<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Config;

final class FileConfig implements \Htw\Config\ConfigInterface
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
