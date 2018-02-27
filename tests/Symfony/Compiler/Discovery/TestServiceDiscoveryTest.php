<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Discovery;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Zalas\PHPUnit\DependencyInjection\Service\Extractor;
use Zalas\PHPUnit\DependencyInjection\Service\TestService;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\Discovery\ClassFinder;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\Discovery\TestServiceDiscovery;
use PHPUnit\Framework\TestCase;
use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;
use Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Fixtures\Service1;
use Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Fixtures\TestCase1;
use Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Fixtures\TestCase2;

class TestServiceDiscoveryTest extends TestCase
{
    /**
     * @var TestServiceDiscovery
     */
    private $discovery;

    /**
     * @var Extractor|ObjectProphecy
     */
    private $extractor;

    /**
     * @var ClassFinder|ObjectProphecy
     */
    private $classFinder;

    protected function setUp()
    {
        $this->extractor = $this->prophesize(Extractor::class);
        $this->classFinder = $this->prophesize(ClassFinder::class);
        $this->discovery = new TestServiceDiscovery($this->extractor->reveal(), $this->classFinder->reveal());

        $this->classFinder->findImplementations(Argument::any())->willReturn([]);
    }

    public function test_it_discovers_test_services_in_service_container_test_cases_from_the_given_namespace()
    {
        $testService1 = new TestService(TestCase1::class, 'service1', Service1::class);
        $testService2 = new TestService(TestCase1::class, 'service2', Service1::class);
        $testService2again = new TestService(TestCase2::class, 'service2', Service2::class);

        $this->classFinder->findImplementations(ServiceContainerTestCase::class)->willReturn([TestCase1::class, TestCase2::class]);

        $this->extractor->extract(TestCase1::class)->willReturn([$testService1, $testService2]);
        $this->extractor->extract(TestCase2::class)->willReturn([$testService2again]);

        $testServices = $this->discovery->run();

        $this->assertNotEmpty($testServices);
        $this->assertCount(3, $testServices);
        $this->assertSame([$testService1, $testService2, $testService2again], $testServices);
    }

    public function test_it_returns_no_services_if_there_is_no_test_service_test_cases_in_the_given_namespace()
    {
        $this->assertEmpty($this->discovery->run());
    }
}
