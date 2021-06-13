<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

interface WorldObjectInterface
{
    public const TYPE_BAT = 'BAT';
    public const TYPE_PIT = 'PIT';
    public const TYPE_WUMPUS = 'WUMPUS';
    public const TYPE_PLAYER = 'PLAYER';

    public function getType(): string;
    public function getId(): int;
}
