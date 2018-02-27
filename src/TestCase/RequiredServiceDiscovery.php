<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\TestCase;

use Zalas\PHPUnit\DependencyInjection\Service\Extractor;
use Zalas\PHPUnit\DependencyInjection\Service\RequiredService;

class RequiredServiceDiscovery
{
    /**
     * @var Extractor
     */
    private $extractor;

    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @return RequiredService[]
     */
    public function run(string $searchNamespace): array
    {
        return $this->flatMap(
            function (string $class) {
                return $this->extractor->extract($class);
            },
            $this->findTestCases($searchNamespace)
        );
    }

    private function findTestCases(string $searchNamespace): array
    {
        return array_filter(get_declared_classes(), function (string $class) use ($searchNamespace) {
            return 0 === strpos($class, $searchNamespace) && in_array(ServiceContainerTestCase::class, class_implements($class));
        });
    }

    private function flatMap(callable $callback, array $collection): array
    {
        return array_merge([], ...array_map($callback, $collection));
    }
}