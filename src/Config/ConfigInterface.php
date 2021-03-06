<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\Config;

interface ConfigInterface
{
    /**
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public function getParam(string $param, $default = null);
}
