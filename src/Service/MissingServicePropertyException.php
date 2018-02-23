<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service;

final class MissingServicePropertyException extends \LogicException
{
    public function __construct(string $class, string $propertyName)
    {
        parent::__construct(sprintf('The `%s::%s` property does not exist. ', $class, $propertyName));
    }

}