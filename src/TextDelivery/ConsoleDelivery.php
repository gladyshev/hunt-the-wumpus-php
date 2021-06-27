<?php
/**
 * @project Hunt the Wumpus
 */

namespace Htw\TextDelivery;

/**
 * Class ConsoleDelivery
 *
 * @author Dmitry Gladyshev <gladyshevd@icloud.com>
 */
class ConsoleDelivery implements TextDeliveryInterface
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