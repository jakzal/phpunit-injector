<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Symfony\Compiler;

use Symfony\Component\Config\Resource\ReflectionClassResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\Injector\PHPUnit\Symfony\Compiler\Discovery\PropertyDiscovery;
use Zalas\Injector\Service\Property;

class ExposeServicesForTestsPass implements CompilerPassInterface
{
    /**
     * @var PropertyDiscovery
     */
    private $propertyDiscovery;

    public function __construct(?PropertyDiscovery $propertyDiscovery = null)
    {
        $this->propertyDiscovery = $propertyDiscovery ?? new PropertyDiscovery();
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($this->discoverServices() as $testClass => $references) {
            $container->register($testClass, ServiceLocator::class)
                ->setPublic(true)
                ->addTag('container.service_locator')
                ->addArgument($references);

            $container->addResource(new ReflectionClassResource(new \ReflectionClass($testClass)));
        }
    }

    private function discoverServices(): array
    {
        return \array_reduce($this->propertyDiscovery->run(), function (array $services, Property $property) {
            $services[$property->getClassName()][$property->getServiceId()] = new Reference($property->getServiceId(), ContainerInterface::IGNORE_ON_INVALID_REFERENCE);

            return $services;
        }, []);
    }
}
