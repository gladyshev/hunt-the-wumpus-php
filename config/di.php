<?php

return [
    \Wumpus\GameRules\TextDeliveryInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return $container->get(\Wumpus\TextDelivery\AnsiConsoleDelivery::class);
    },

    \Wumpus\GameRules\DataStorageInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return new \Wumpus\Storage\MemoryDataStorage;
    },

    \Wumpus\GameRules\UIInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return $container->get(\Wumpus\UI\UI::class);
    },

    \Wumpus\UI\I18NInterface::class => function (\Psr\Container\ContainerInterface $container) {
        $config = $container->get(\Wumpus\GameRules\ConfigInterface::class);
        return new \Wumpus\UI\I18N(
            $config->getParam('lang_path'),
            $config->getParam('lang')
        );
    },

    \Wumpus\GameRules\ConfigInterface::class => function() {
        return new \Wumpus\Config\FileConfig('config/options.php');
    }
];
