<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service\Exception;

use Zalas\PHPUnit\DependencyInjection\Service\Exception;

final class MissingServiceException extends \RuntimeException implements Exception
{
    public function __construct(string $serviceId, string $class, string $propertyName, \Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                'The `%s` service cannot be injected into `%s::%s` as it could not be found in the container.',
                $serviceId,
                $class,
                $propertyName
            ),
            0,
            $previous
        );
    }
}
