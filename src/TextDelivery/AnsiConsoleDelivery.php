<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\TextDelivery;

/**
 * Class AnsiTextTextIO
 *
 * @author Dmitry Gladyshev <gladyshevd@icloud.com>
 */
class AnsiConsoleDelivery implements TextDeliveryInterface
{
    public function output(string $message = ''): void
    {
        fwrite(\STDOUT, $message);
    }

    public function input(): string
    {
        return mb_strtoupper(rtrim(fgets(\STDIN), PHP_EOL));
    }
}