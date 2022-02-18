<?php

return [
    \Htw\TextDelivery\TextDeliveryInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return $container->get(\Htw\TextDelivery\ConsoleDelivery::class);
    },

    \Htw\IO\IOInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return $container->get(\Htw\IO\IO::class);
    },

    \Htw\I18N\I18NInterface::class => function (\Psr\Container\ContainerInterface $container) {
        $config = $container->get(\Htw\Config\ConfigInterface::class);
        return new \Htw\I18N\I18N(
            $config->getParam('lang_path'),
            $config->getParam('lang')
        );
    },

    \Htw\Config\ConfigInterface::class => function() {
        return new \Htw\Config\FileConfig('config/options.php');
    },
];
