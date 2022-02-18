<?php

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
file_exists(__DIR__.'/../.env') && \Dotenv\Dotenv::createImmutable(__DIR__.'/../')->load();

return (new \DI\ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions(require __DIR__ . '/dependencies.php')
    ->build();
