<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\NoFrameworkBundle;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Zalas\Injector\PHPUnit\Symfony\Compiler\Discovery\ClassFinder;
use Zalas\Injector\PHPUnit\Symfony\Compiler\Discovery\PropertyDiscovery;
use Zalas\Injector\PHPUnit\Symfony\Compiler\ExposeServicesForTestsPass;
use Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\Service1;
use Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\Service2;

class TestKernel extends Kernel
{
    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundles(): array
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return \sys_get_temp_dir().'/ZalasPHPUnitInjector/NoFrameworkBundle/TestKernel/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return \sys_get_temp_dir().'/ZalasPHPUnitInjector/NoFrameworkBundle/TestKernel/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->register(Service1::class, Service1::class);
            $container->register('foo.service2', Service2::class);
        });
    }

    protected function build(ContainerBuilder $container): void
    {
        if ('test' === $this->getEnvironment()) {
            $container->addCompilerPass(
                new ExposeServicesForTestsPass(
                    new PropertyDiscovery(new ClassFinder(__DIR__ . '/../../'))
                )
            );
        }
    }
}
