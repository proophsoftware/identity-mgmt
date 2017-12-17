<?php
declare(strict_types = 1);

$config = include 'config.php';

$serviceFactory = new \App\Service\ServiceFactory($config);

//@TODO use cached serviceFactoryMap for production
$container = new \Prooph\EventMachine\Container\ReflectionBasedContainer(
    $serviceFactory,
    [
        \Prooph\EventMachine\EventMachine::SERVICE_ID_EVENT_STORE => \Prooph\EventStore\EventStore::class,
        \Prooph\EventMachine\EventMachine::SERVICE_ID_COMMAND_BUS => \Prooph\ServiceBus\CommandBus::class,
        \Prooph\EventMachine\EventMachine::SERVICE_ID_EVENT_BUS => \Prooph\ServiceBus\EventBus::class,
    ]
);

$serviceFactory->setContainer($container);

return $container;