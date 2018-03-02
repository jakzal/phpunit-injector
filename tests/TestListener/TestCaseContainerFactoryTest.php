<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\TestListener;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\PHPUnit\TestListener\TestCaseContainerFactory;
use Zalas\Injector\Service\ContainerFactory;

class TestCaseContainerFactoryTest extends TestCase
{
    /**
     * @var TestCaseContainerFactory
     */
    private $factory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ServiceContainerTestCase|ObjectProphecy
     */
    private $testCase;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class)->reveal();
        $this->testCase = $this->prophesize(ServiceContainerTestCase::class);

        $this->factory = new TestCaseContainerFactory($this->testCase->reveal());
    }

    public function test_it_is_a_container_factory()
    {
        $this->assertInstanceOf(ContainerFactory::class, $this->factory);
    }

    public function test_it_uses_the_test_case_to_create_a_container()
    {
        $this->testCase->createContainer()->willReturn($this->container);

        $createdContainer = $this->factory->create();

        $this->assertSame($this->container, $createdContainer);
    }
}
