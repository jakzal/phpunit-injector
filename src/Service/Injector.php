<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service;
use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionProperty;
use Zalas\PHPUnit\DependencyInjection\Service\Exception\FailedToInjectServiceException;
use Zalas\PHPUnit\DependencyInjection\Service\Exception\MissingServiceException;

/**
 * Injects services into properties of the given object.
 */
class Injector
{
    /**
     * @var Extractor
     */
    private $extractor;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(Extractor $extractor, ContainerInterface $container)
    {
        $this->extractor = $extractor;
        $this->container = $container;
    }

    /**
     * @throws MissingServiceException
     * @throws FailedToInjectServiceException
     */
    public function inject(object $object): void
    {
        array_map($this->getPropertyInjector($object), $this->extractProperties($object));
    }

    /**
     * @return RequiredService[]
     */
    private function extractProperties(object $object): array
    {
        return $this->extractor->extract(get_class($object));
    }

    private function getPropertyInjector(object $object): Closure
    {
        return function (RequiredService $property) use ($object) {
            return $this->injectService($object, $property);
        };
    }

    private function injectService(object $object, RequiredService $property): void
    {
        $reflectionProperty = new ReflectionProperty($property->getClassName(), $property->getPropertyName());
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $this->getService($property));
    }

    private function getService(RequiredService $property)
    {
        try {
            return $this->container->get($property->getServiceId());
        } catch (NotFoundExceptionInterface $e) {
            throw new MissingServiceException($property->getServiceId(), $property->getClassName(), $property->getPropertyName(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new FailedToInjectServiceException($property->getServiceId(), $property->getClassName(), $property->getPropertyName(), $e);
        }
    }
}
