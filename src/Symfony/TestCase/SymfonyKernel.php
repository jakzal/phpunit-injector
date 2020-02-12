<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Symfony\TestCase;

use Symfony\Component\DependencyInjection\ResettableContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Mimics the behaviour of Symfony's KernelTestCase.
 */
trait SymfonyKernel
{
    protected static ?KernelInterface $kernel = null;

    protected static function bootKernel(array $options = []): KernelInterface
    {
        static::ensureKernelShutdown();

        static::$kernel = self::createKernel($options);
        static::$kernel->boot();

        return static::$kernel;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        $kernelClass = static::getKernelClass($options);
        $environment = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = (bool) ($options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true);

        return new $kernelClass($environment, $debug);
    }

    protected static function getKernelClass(array $options = []): string
    {
        $kernelClass = $options['kernel_class'] ?? $_ENV['KERNEL_CLASS'] ?? $_SERVER['KERNEL_CLASS'] ?? '';

        if (empty($kernelClass)) {
            throw new \RuntimeException('Configure the kernel class to use in tests by setting the KERNEL_CLASS environment variable or passing the kernel_class option.');
        }

        return $kernelClass;
    }

    /**
     * @after
     */
    protected static function ensureKernelShutdown(): void
    {
        if (static::$kernel instanceof KernelInterface) {
            $container = static::$kernel->getContainer();
            static::$kernel->shutdown();
            if ($container instanceof ResetInterface || $container instanceof ResettableContainerInterface) {
                $container->reset();
            }

            static::$kernel = null;
        }
    }
}
