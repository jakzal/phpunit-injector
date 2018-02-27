<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Symfony\TestCase\Fixtures;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 'abc',
        ]);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'ZalasPHPUnitDependencyInjection/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().'ZalasPHPUnitDependencyInjection/logs';
    }
}
