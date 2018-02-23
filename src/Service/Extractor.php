<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service;

use Zalas\PHPUnit\DependencyInjection\Service\Exception\MissingServiceIdException;

interface Extractor
{
    /**
     * Extracts all the class properties that require a service to be injected.
     *
     * Properties should be tagged with `@inject`.
     * An optional value can be given, which should be used as a service id: `@inject my.service.id`.
     * If the service id is not given, a type should be taken from the `@var` tag.
     *
     * Example:
     *
     * <code>
     * @var Foo\Bar\Baz
     * @inject
     * </code>
     *
     * An exception should be thrown if the service id cannot be determined.
     *
     * @param string $class
     * @return RequiredService[]
     *
     * @throws MissingServiceIdException
     */
    public function extract(string $class): array;
}
