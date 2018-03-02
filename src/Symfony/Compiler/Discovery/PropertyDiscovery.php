<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Symfony\Compiler\Discovery;

use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\Service\ExtractorFactory;
use Zalas\Injector\Service\Property;

class PropertyDiscovery
{
    /**
     * @var ClassFinder
     */
    private $classFinder;

    /**
     * @var ExtractorFactory
     */
    private $extractorFactory;

    public function __construct(?ClassFinder $classFinder = null, ?ExtractorFactory $extractorFactory = null)
    {
        $this->classFinder = $classFinder ?? new ClassFinder();
        $this->extractorFactory = $extractorFactory ?? new DefaultExtractorFactory();
    }

    /**
     * @return Property[]
     */
    public function run(): array
    {
        return $this->flatMap([$this, 'extract'], $this->findTestCases());
    }

    private function findTestCases(): array
    {
        return $this->classFinder->findImplementations(ServiceContainerTestCase::class);
    }

    private function extract(string $class): array
    {
        return $this->extractorFactory->create()->extract($class);
    }

    private function flatMap(callable $callback, array $collection): array
    {
        return \array_merge([], ...\array_map($callback, $collection));
    }
}
