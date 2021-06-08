<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\GameRules;

final class Hazard implements WorldObjectInterface
{
    public const TYPE_BAT = 'BAT';
    public const TYPE_PIT = 'PIT';
    public const TYPE_WUMPUS = 'WUMPUS';

    private string $type;
    private int $id;

    public function __construct(
        string $type,
        int $id
    ) {
        $this->type = $type;
        $this->id = $id;
    }

    public static function fromType(string $type): self
    {
        return new self($type, rand(100, PHP_INT_MAX));
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
