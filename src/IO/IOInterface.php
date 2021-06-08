<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\IO;

interface IOInterface
{
    public function print(string $message = '', array $placeholders = []): void;
    public function println(string $message = '', array $placeholders = []): void;
    public function input(string $prompt = '', string $default = '', array $placeholders = []): string;
}