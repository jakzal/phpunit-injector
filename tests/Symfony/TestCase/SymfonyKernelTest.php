<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyKernel;
use Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel;

class SymfonyKernelTest extends TestCase
{
    use SymfonyKernel;

    public function test_it_throws_an_exception_if_kernel_class_is_not_configured()
    {
        $this->expectException(\RuntimeException::class);

        self::bootKernel();
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     */
    public function test_it_boots_the_kernel_in_test_environment_with_debug_enabled_by_default()
    {
        $kernel = self::bootKernel();

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertTrue($kernel->isDebug());
        $this->assertKernelIsBooted($kernel);
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     * @env APP_ENV=test_foo
     * @env APP_DEBUG=0
     */
    public function test_it_boots_the_kernel_configured_via_env_variable()
    {
        $kernel = self::bootKernel();

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
        $this->assertKernelIsBooted($kernel);
    }

    /**
     * @server KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     * @server APP_ENV=test_foo
     * @server APP_DEBUG=0
     */
    public function test_it_boots_the_kernel_configured_via_server_variable()
    {
        $kernel = self::bootKernel();

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
        $this->assertKernelIsBooted($kernel);
    }

    public function test_it_boots_the_kernel_configured_via_options()
    {
        $kernel = self::bootKernel([
            'kernel_class' => TestKernel::class,
            'environment' => 'test_foo',
            'debug' => false,
        ]);

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
        $this->assertKernelIsBooted($kernel);
    }

    /**
     * @env KERNEL_CLASS=Foo
     * @env APP_ENV=test
     * @env APP_DEBUG=1
     * @server KERNEL_CLASS=Bar
     * @server APP_ENV=test
     * @server APP_DEBUG=1
     */
    public function test_it_prefers_options_over_env_variables()
    {
        $kernel = self::bootKernel([
            'kernel_class' => TestKernel::class,
            'environment' => 'test_foo',
            'debug' => false,
        ]);

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     * @env APP_ENV=test_foo
     * @env APP_DEBUG=0
     * @server KERNEL_CLASS=Bar
     * @server APP_ENV=test
     * @server APP_DEBUG=1
     */
    public function test_it_prefers_env_variables_over_server()
    {
        $kernel = self::bootKernel();

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     */
    public function test_it_ensures_the_kernel_was_shut_down()
    {
        $kernel1 = self::bootKernel();
        $kernel2 = self::bootKernel();

        $this->assertNull($kernel1->getContainer());
        $this->assertNotNull($kernel2->getContainer());
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     */
    public function test_ensureKernelShutdown_shuts_down_the_kernel()
    {
        $kernel = self::bootKernel();

        self::ensureKernelShutdown();

        $this->assertNull($kernel->getContainer());
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     */
    public function test_ensureKernelShutdown_resets_the_container()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $container->set('foo.bar', new \stdClass());

        self::ensureKernelShutdown();

        $this->assertFalse($container->has('foo.bar'));
    }

    public function test_it_starts_in_a_fresh_state()
    {
        $this->assertNull(self::$kernel);
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     */
    public function test_it_creates_the_kernel_in_test_environment_with_debug_enabled_by_default()
    {
        $kernel = self::createKernel();

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test', $kernel->getEnvironment());
        $this->assertTrue($kernel->isDebug());
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     * @env APP_ENV=test_foo
     * @env APP_DEBUG=0
     */
    public function test_it_creates_the_kernel_configured_via_env_variable()
    {
        $kernel = self::createKernel();

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
    }

    /**
     * @server KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     * @server APP_ENV=test_foo
     * @server APP_DEBUG=0
     */
    public function test_it_creates_the_kernel_configured_via_server_variable()
    {
        $kernel = self::createKernel();

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
    }

    public function test_it_creates_the_kernel_configured_via_options()
    {
        $kernel = self::createKernel([
            'kernel_class' => TestKernel::class,
            'environment' => 'test_foo',
            'debug' => false,
        ]);

        $this->assertInstanceOf(TestKernel::class, $kernel);
        $this->assertSame('test_foo', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
    }

    private function assertKernelIsBooted(KernelInterface $kernel)
    {
        $this->assertNotNull($kernel->getContainer(), 'Container was created.');
    }
}
