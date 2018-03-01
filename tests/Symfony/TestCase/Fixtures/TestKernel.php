<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\Symfony\TestCase\Fixtures;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\Injector\PhpDocumentor\ReflectionExtractor;
use Zalas\PHPUnit\Injector\Symfony\Compiler\Discovery\ClassFinder;
use Zalas\PHPUnit\Injector\Symfony\Compiler\Discovery\PropertyDiscovery;
use Zalas\PHPUnit\Injector\Symfony\Compiler\ExposeServicesForTestsPass;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }

    protected function build(ContainerBuilder $container)
    {
        if ('test' === $this->getEnvironment()) {
            $container->addCompilerPass(
                new ExposeServicesForTestsPass(
                    ExposeServicesForTestsPass::DEFAULT_SERVICE_LOCATOR_ID,
                    new PropertyDiscovery(new ClassFinder(__DIR__ . '/../'))
                )
            );
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 'abc',
        ]);
        $c->register('foo', \stdClass::class);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'ZalasPHPUnitInjector/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().'ZalasPHPUnitInjector/logs';
    }
}
