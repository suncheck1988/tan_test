<?php

declare(strict_types=1);

use App\Application\Service\Validator\Validator;
use DI\Definition\Helper\CreateDefinitionHelper;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;

$actionInit = static fn (): CreateDefinitionHelper => \DI\autowire()->method(
    'init',
    \DI\get(Validator::class),
    \DI\get(CommandBus::class),
    \DI\get(LoggerInterface::class),
);

return [
    'App\UI\Http\Action\*\*Action' => $actionInit(),
    'App\UI\Http\Action\*\*\*Action' => $actionInit(),
    'App\UI\Http\Action\*\*\*\*Action' => $actionInit(),
];
