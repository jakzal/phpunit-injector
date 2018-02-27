<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Symfony\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\PHPUnit\DependencyInjection\Service\RequiredService;
use Zalas\PHPUnit\DependencyInjection\TestCase\RequiredServiceDiscovery;

class ExposeServicesForTestsPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $serviceLocatorId;

    /**
     * @var RequiredServiceDiscovery
     */
    private $requiredServiceDiscovery;

    public function __construct(string $serviceLocatorId, RequiredServiceDiscovery $requiredServiceDiscovery)
    {
        $this->serviceLocatorId = $serviceLocatorId;
        $this->requiredServiceDiscovery = $requiredServiceDiscovery;
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
            function (RequiredService $service) {
                return [$service->getServiceId() => new Reference($service->getServiceId())];
            },
            $this->requiredServiceDiscovery->run()
        );
    }

    private function flatMap(callable $callback, array $collection): array
    {
        return array_merge([], ...array_map($callback, $collection));
    }
}
