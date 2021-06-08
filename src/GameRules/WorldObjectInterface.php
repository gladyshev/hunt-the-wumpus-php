<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

interface WorldObjectInterface
{
    public function getType(): string;
    public function getId(): int;
}
