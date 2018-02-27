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
     * @var ContainerFactory
     */
    private $containerFactory;

    public function __construct(Extractor $extractor, ContainerFactory $containerFactory)
    {
        $this->extractor = $extractor;
        $this->containerFactory = $containerFactory;
    }

    /**
     * @throws MissingServiceException
     * @throws FailedToInjectServiceException
     */
    public function inject(object $object): void
    {
        array_map($this->getPropertyInjector($object), $this->extractTestServices($object));
    }

    /**
     * @return TestService[]
     */
    private function extractTestServices(object $object): array
    {
        return $this->extractor->extract(get_class($object));
    }

    private function getPropertyInjector(object $object): Closure
    {
        $container = $this->containerFactory->create();

        return function (TestService $testService) use ($object, $container) {
            return $this->injectService($object, $testService, $container);
        };
    }

    private function injectService(object $object, TestService $testService, ContainerInterface $container): void
    {
        $reflectionProperty = new ReflectionProperty($testService->getClassName(), $testService->getPropertyName());
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $this->getService($container, $testService));
    }

    private function getService(ContainerInterface $container, TestService $testService)
    {
        try {
            return $container->get($testService->getServiceId());
        } catch (NotFoundExceptionInterface $e) {
            throw new MissingServiceException($testService->getServiceId(), $testService->getClassName(), $testService->getPropertyName(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new FailedToInjectServiceException($testService->getServiceId(), $testService->getClassName(), $testService->getPropertyName(), $e);
        }
    }
}
