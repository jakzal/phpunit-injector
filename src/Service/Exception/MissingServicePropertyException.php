<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service\Exception;

use Zalas\PHPUnit\DependencyInjection\Service\Exception;

final class MissingServicePropertyException extends \LogicException implements Exception
{
    public function __construct(string $class, string $propertyName)
    {
        parent::__construct(sprintf('The `%s::%s` property does not exist. ', $class, $propertyName));
    }
}