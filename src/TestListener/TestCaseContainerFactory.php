<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\TestListener;

use Psr\Container\ContainerInterface;
use Zalas\Injector\Service\ContainerFactory;
use Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase;

final class TestCaseContainerFactory implements ContainerFactory
{
    /**
     * @var ServiceContainerTestCase
     */
    private $testCase;

    public function __construct(ServiceContainerTestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    public function create(): ContainerInterface
    {
        return $this->testCase->createContainer();
    }
}