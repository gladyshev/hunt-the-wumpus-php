<?php
/**
 * @project Hunt the Wumpus
 */

namespace Wumpus\GameRules;

interface UIInterface
{
    public function print(string $message = '', array $placeholders = []): void;
    public function println(string $message = '', array $placeholders = []): void;
    public function input(string $prompt = '', string $default = '', array $placeholders = []): string;
}