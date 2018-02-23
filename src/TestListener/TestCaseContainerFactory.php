<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\TestListener;

use Psr\Container\ContainerInterface;
use Zalas\PHPUnit\DependencyInjection\Service\ContainerFactory;
use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;

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

    public function create(array $requiredServices = []): ContainerInterface
    {
        return $this->testCase->createContainer($requiredServices);
    }
}