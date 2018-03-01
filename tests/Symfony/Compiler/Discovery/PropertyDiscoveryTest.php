<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\Symfony\Compiler\Discovery;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Zalas\Injector\Service\Extractor;
use Zalas\Injector\Service\ExtractorFactory;
use Zalas\Injector\Service\Property;
use Zalas\PHPUnit\Injector\Symfony\Compiler\Discovery\ClassFinder;
use Zalas\PHPUnit\Injector\Symfony\Compiler\Discovery\PropertyDiscovery;
use Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase;
use Zalas\PHPUnit\Injector\Tests\Symfony\Compiler\Fixtures\Service1;
use Zalas\PHPUnit\Injector\Tests\Symfony\Compiler\Fixtures\TestCase1;
use Zalas\PHPUnit\Injector\Tests\Symfony\Compiler\Fixtures\TestCase2;

class PropertyDiscoveryTest extends TestCase
{
    /**
     * @var PropertyDiscovery
     */
    private $discovery;

    /**
     * @var ExtractorFactory|ObjectProphecy
     */
    private $extractorFactory;
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
        $this->extractorFactory = $this->prophesize(ExtractorFactory::class);
        $this->extractor = $this->prophesize(Extractor::class);
        $this->classFinder = $this->prophesize(ClassFinder::class);

        $this->discovery = new PropertyDiscovery($this->classFinder->reveal(), $this->extractorFactory->reveal());

        $this->classFinder->findImplementations(Argument::any())->willReturn([]);
        $this->extractorFactory->create()->willReturn($this->extractor);
    }

    public function test_it_discovers_test_services_in_service_container_test_cases_from_the_given_namespace()
    {
        $property1 = new Property(TestCase1::class, 'service1', Service1::class);
        $property2 = new Property(TestCase1::class, 'service2', Service1::class);
        $property2again = new Property(TestCase2::class, 'service2', Service2::class);

        $this->classFinder->findImplementations(ServiceContainerTestCase::class)->willReturn([TestCase1::class, TestCase2::class]);

        $this->extractor->extract(TestCase1::class)->willReturn([$property1, $property2]);
        $this->extractor->extract(TestCase2::class)->willReturn([$property2again]);

        $properties = $this->discovery->run();

        $this->assertNotEmpty($properties);
        $this->assertCount(3, $properties);
        $this->assertSame([$property1, $property2, $property2again], $properties);
    }

    public function test_it_returns_no_services_if_there_is_no_test_service_test_cases_in_the_given_namespace()
    {
        $this->assertEmpty($this->discovery->run());
    }
}
