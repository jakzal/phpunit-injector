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
        array_map($this->getPropertyInjector($object), $this->extractRequiredServices($object));
    }

    /**
     * @return RequiredService[]
     */
    private function extractRequiredServices(object $object): array
    {
        return $this->extractor->extract(get_class($object));
    }

    private function getPropertyInjector(object $object): Closure
    {
        $container = $this->containerFactory->create();

        return function (RequiredService $requiredService) use ($object, $container) {
            return $this->injectService($object, $requiredService, $container);
        };
    }

    private function injectService(object $object, RequiredService $requiredService, ContainerInterface $container): void
    {
        $reflectionProperty = new ReflectionProperty($requiredService->getClassName(), $requiredService->getPropertyName());
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $this->getService($container, $requiredService));
    }

    private function getService(ContainerInterface $container, RequiredService $requiredService)
    {
        try {
            return $container->get($requiredService->getServiceId());
        } catch (NotFoundExceptionInterface $e) {
            throw new MissingServiceException($requiredService->getServiceId(), $requiredService->getClassName(), $requiredService->getPropertyName(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new FailedToInjectServiceException($requiredService->getServiceId(), $requiredService->getClassName(), $requiredService->getPropertyName(), $e);
        }
    }
}
