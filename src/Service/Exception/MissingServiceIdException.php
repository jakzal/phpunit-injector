<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service\Exception;

use Zalas\PHPUnit\DependencyInjection\Service\Exception;

final class MissingServiceIdException extends \LogicException implements Exception
{
    public function __construct(string $class, string $propertyName)
    {
        parent::__construct(
            sprintf(
                'The `%s::%s` property was configured for service injection, but no service type nor id was given. '.PHP_EOL.
                'Add the `@var type`, or `@inject my.service.id` tag.',
                $class,
                $propertyName
            )
        );
    }
}