<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\TestListener;

use Psr\Container\ContainerInterface;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\Service\ContainerFactory;

final class TestCaseContainerFactory implements ContainerFactory
{
    private ServiceContainerTestCase $testCase;

    public function __construct(ServiceContainerTestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    public function create(): ContainerInterface
    {
        return $this->testCase->createContainer();
    }
}
