<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\FrameworkBundle;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
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
        return [
            new FrameworkBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return \sys_get_temp_dir().'/ZalasPHPUnitInjector/FrameworkBundle/TestKernel/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return \sys_get_temp_dir().'/ZalasPHPUnitInjector/FrameworkBundle/TestKernel/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->register(Service1::class, Service1::class);
            $container->register('foo.service2', Service2::class);
            $container->register('public_service', \stdClass::class)
                ->setPublic(true)
                ->setArguments([
                    new Reference(Service1::class),
                    new Reference('foo.service2')
                ]);

            $container->loadFromExtension('framework', ['test' => true]);
            $container->setParameter('kernel.secret', '$ecre7');
        });
    }
}
