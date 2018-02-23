<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service\Exception;

use Zalas\PHPUnit\DependencyInjection\Service\Exception;

final class FailedToInjectServiceException extends \RuntimeException implements Exception
{
    public function __construct(string $serviceId, string $class, string $propertyName, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                'Failed to inject the `%s` service into `%s::%s`.',
                $serviceId,
                $class,
                $propertyName
            ),
            0,
            $previous
        );
    }
}
