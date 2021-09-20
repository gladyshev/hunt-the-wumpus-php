<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\IO;

interface IOInterface
{
    public function print(
        string $messageId = '',
        array $placeholders = []
    ): void;

    public function println(
        string $messageId = '',
        array $placeholders = []
    ): void;

    public function inputln(
        string $promptMessageId = '',
        string $default = '',
        array $placeholders = []
    ): string;
}
