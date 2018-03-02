<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Discovery;

use PHPUnit\Framework\TestCase;
use Zalas\Injector\PHPUnit\Symfony\Compiler\Discovery\ClassFinder;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures\TestCase1;
use Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures\TestCase2;

class ClassFinderTest extends TestCase
{
    public function test_it_finds_classes_that_implement_given_interface()
    {
        $classFinder = new ClassFinder(__DIR__ . '/../Fixtures');

        $classes = $classFinder->findImplementations(ServiceContainerTestCase::class);

        $this->assertSame([TestCase1::class, TestCase2::class], $classes);
    }
}
