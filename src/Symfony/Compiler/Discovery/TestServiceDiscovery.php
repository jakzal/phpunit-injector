<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\Discovery;

use Zalas\PHPUnit\DependencyInjection\Service\Extractor;
use Zalas\PHPUnit\DependencyInjection\Service\TestService;
use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;

class TestServiceDiscovery
{
    /**
     * @var Extractor
     */
    private $extractor;

    /**
     * @var ClassFinder
     */
    private $classFinder;

    public function __construct(Extractor $extractor, ?ClassFinder $classFinder = null)
    {
        $this->extractor = $extractor;
        $this->classFinder = $classFinder ?? new ClassFinder();
    }

    /**
     * @return TestService[]
     */
    public function run(): array
    {
        return $this->flatMap(
            function (string $class) {
                return $this->extractor->extract($class);
            },
            $this->findTestCases()
        );
    }

    private function findTestCases(): array
    {
        return $this->classFinder->findImplementations(ServiceContainerTestCase::class);
    }

    private function flatMap(callable $callback, array $collection): array
    {
        return array_merge([], ...array_map($callback, $collection));
    }
}