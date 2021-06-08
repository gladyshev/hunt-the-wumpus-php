<?php

namespace Htw\TextDelivery;

interface TextDeliveryInterface
{
    public function input(): string;
    public function output(string $message): void;
}
