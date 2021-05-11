<?php

namespace Wumpus\GameRules;

interface TextDeliveryInterface
{
    public function input(): string;
    public function output(string $message): void;
}
