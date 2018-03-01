<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Symfony\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\Injector\Service\Property;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\Discovery\PropertyDiscovery;

class ExposeServicesForTestsPass implements CompilerPassInterface
{
    const DEFAULT_SERVICE_LOCATOR_ID = 'app.test.service_locator';

    /**
     * @var string
     */
    private $serviceLocatorId;

    /**
     * @var PropertyDiscovery
     */
    private $propertyDiscovery;

    public function __construct(string $serviceLocatorId = self::DEFAULT_SERVICE_LOCATOR_ID, ?PropertyDiscovery $propertyDiscovery = null)
    {
        $this->serviceLocatorId = $serviceLocatorId;
        $this->propertyDiscovery = $propertyDiscovery ?? new PropertyDiscovery();
    }

    public function process(ContainerBuilder $container): void
    {
        $container->register($this->serviceLocatorId, ServiceLocator::class)
            ->setPublic(true)
            ->addTag('container.service_locator')
            ->addArgument($this->discoverServices());
    }

    private function discoverServices(): array
    {
        return $this->flatMap(
            function (Property $property) {
                return [$property->getServiceId() => new Reference($property->getServiceId())];
            },
            $this->propertyDiscovery->run()
        );
    }

    private function flatMap(callable $callback, array $collection): array
    {
        return array_merge([], ...array_map($callback, $collection));
    }
}
